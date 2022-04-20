<?php

namespace Rhf\Modules\Content\Services;

use Carbon\Carbon;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessPreconditionException;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Models\Category;
use Rhf\Modules\System\Services\FacebookExtender;
use Illuminate\Support\Facades\Redis;
use Rhf\Modules\Content\Services\ContentService;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserRole;

class FacebookContentService extends ContentService
{
    // API Status
    protected $apiConnected = false; // Only connect to the API if needed to avoid throttling

    // APIs
    protected $fb;
    protected $appApi;
    protected $pageApi;
    protected $userApi;

    // Posts
    protected $groupPosts;
    protected $pagePosts;
    protected $pageVideoPosts;
    protected $post;
    protected $posts;

    // Filtered types (If post has type in this array it will not be returned
    private $filteredTypes = ['status', 'link'];

    /**
     * Create a new Service instance.
     *
     * @throws \Exception
     * @return void
     */
    public function __construct(FacebookExtender $fb = null)
    {
        parent::__construct();
        $this->fb = new FacebookExtender();

        // Instantiate the connection if non exists
        if (!$fb) {
            // Retrieve and admin user's token
            $adminGodRoles = UserRole::whereIn('slug', ['admin', 'higher-admin', 'god'])->pluck('id')->toArray();
            $admin = User::whereIn('role_id', $adminGodRoles)->where('token', '!=', 'NULL')->first();

            if ($admin && $admin->token != null) {
                $this->fb->setDefaultAccessToken(
                    config('services.facebook.client_id') . '|' . config('services.facebook.client_secret')
                );
                $this->appApi = $this->fb;
                $this->userApi = (clone $this->fb);
                $this->userApi->setDefaultAccessToken($admin->token);
                $this->pageApi = (clone $this->userApi);
            } else {
                throw new FitnessBadRequestException(
                    'Unable to retrieve Facebook content as your user. Please contact RH Fitness Support.'
                );
            }
        } else {
            $fb->setDefaultAccessToken(
                config('services.facebook.client_id') . '|' . config('services.facebook.client_secret')
            );
            $this->appApi = $fb;
            $this->userApi = (clone $fb);
            $this->userApi->setDefaultAccessToken(Auth::user()->token);
            $this->pageApi = (clone $this->userApi);
            $this->pageApi->authPageAccessToken();
        }
    }

    /**
     * Connect the API instance when required.
     *
     * @return void
     */
    private function connectFacebookApi()
    {
        if (!$this->apiConnected) {
            $this->pageApi->authPageAccessToken();
            $this->apiConnected = true;
        }
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Retrieve content from the page and the group and combine to local collection.
     *
     * @return array
     */
    public function allPosts()
    {
        $redisId = 'facebook:allPosts';

        // Check redis store
        if (Redis::get($redisId)) {
            $this->posts = json_decode(Redis::get($redisId), true);
        } else {
            if (!$this->groupPosts) {
                $this->fromGroup();
            }
            if (!$this->pagePosts) {
                $this->fromPage();
            }
            if (!$this->pageVideoPosts) {
                $this->fromPageVideos();
            }

            $this->posts = array_merge($this->groupPosts, $this->pagePosts);

            // Set redis store
            Redis::set($redisId, json_encode($this->posts), 'EX', 300);
        }

        return $this->output();
    }

    /**
     * Work out the type of post and retrieve it.
     *
     * @return array
     */
    public function byId($id)
    {
        // If no underscore then it's a video
        if (strpos($id, '_') !== false) {
            return $this->fromPageById($id);
        } else {
            return $this->videoById($id);
        }
    }

    /**
     * Retrieve content from the group.
     *
     * @return array
     */
    public function fromGroup($limit = 100)
    {
        try {
            $this->connectFacebookApi();

            $params = "created_time,message,story,picture,full_picture,properties,type,source,link";
            $this->groupPosts = $this->userApi
                ->get('/769779429792250/feed?limit=' . $limit . '&fields=' . $params)
                ->getGraphEdge()
                ->asArray();

            if (!$this->groupPosts) {
                $this->groupPosts = [];
            }
        } catch (FacebookSDKException $e) {
            Log::error($e);
            $this->groupPosts = [];
        }

        return $this->groupPosts;
    }

    /**
     * Retrieve content from the page.
     *
     * @return array
     */
    public function fromPage($limit = 100)
    {
        try {
            $this->connectFacebookApi();

            $params = "created_time,message,story,picture,full_picture,properties,type,source,link";
            $this->pagePosts = $this->pageApi
                ->get(
                    '/teamrhfitness/posts?limit=' . $limit
                        . '&&include_hidden=true&include_unpublished=truefields=' . $params
                )
                ->getGraphEdge()
                ->asArray();

            if (!$this->pagePosts) {
                $this->pagePosts = [];
            }
        } catch (FacebookSDKException $e) {
            Log::error($e);
            $this->pagePosts = [];
        }

        return $this->pagePosts;
    }

    /**
     * Retrieve content from the page videos section.
     *
     * @return array
     */
    public function fromPageVideos($limit = 100)
    {
        try {
            $this->connectFacebookApi();

            $params = "created_time,message,story,picture,full_picture,properties,type,source,link";
            $this->pageVideoPosts = $this->pageApi
                ->get(
                    '/teamrhfitness/videos?limit=' . $limit
                        . '&include_hidden=true&include_unpublished=true&include_inline=true'
                )
                ->getGraphEdge()
                ->asArray();

            if (!$this->pageVideoPosts) {
                $this->pageVideoPosts = [];
            }
        } catch (FacebookSDKException $e) {
            Log::error($e);
            $this->pageVideoPosts = [];
        }

        return $this->pageVideoPosts;
    }

    /**
     * Retrieve a single post by ID from the page.
     *
     * @return array
     */
    public function fromPageById($id)
    {
        $redisId = 'facebook:fromPageById:' . $id;

        // Check redis store
        if (Redis::get($redisId)) {
            $this->post = (array) json_decode(Redis::get($redisId));

            // Transform dynamic params where required
            $this->post['created_time'] = Carbon::parse($this->post['created_time']->date);

            return $this->post;
        }

        $this->connectFacebookApi();

        try {
            $params = "created_time,message,story,picture,full_picture,properties,type,source,link";
            $post = $this->pageApi->get('/' . $id . '?fields=' . $params)->getGraphNode()->asArray();
        } catch (FacebookSDKException $e) {
            Log::error($e);
            throw new FitnessPreconditionException('Unable to retrieve video content. Please try again later.');
        }

        // Transform
        $post['created_at'] = $post['created_time']->format('d/m/Y H:i');
        if ($this->isLocallyActive($post['id'])) {
            $post['status'] = 'Active';
        } else {
            $post['status'] = 'Inactive';
        }

        // Set Redis store
        Redis::set($redisId, json_encode($post), 'EX', 3600);

        return $this->post = $post;
    }

    /**
     * Get or create the static category associated with Facebook content.
     *
     * @return object Category
     */
    public function getCategory()
    {
        $category = Category::where('title', '=', 'News Feed')->first();
        if (!$category) {
            $category = new Category();
            $category->title = 'News Feed';
            $category->save();
        }
        return $category;
    }

    /**
     * Check if the passed post id(Facebook ID) is already active in the local content.
     *
     * @param int
     * @return bool
     */
    public function isLocallyActive($id)
    {
        if (!isset($this->localPostIds)) {
            $this->localPostIds = Content::isFacebook()->pluck('facebook_id')->toArray();
        }

        return in_array($id, $this->localPostIds);
    }

    /**
     * Take retrieved data and migrate to local content store.
     *
     * @return void
     * @throws \Exception
     */
    public function migrate()
    {
        if (!Content::where('facebook_id', '=', $this->post['id'])->count()) {
            $this->content = new Content();
            $this->update([
                'category_id'   => $this->getCategory()->id,
                'title'         => $this->getPostTitle(),
                'type'          => $this->getPostType(),
                'content'       => $this->getPostContent(),
                'description'   => isset($this->post['message']) ? $this->post['message'] : '',
                'image'         => $this->post['full_picture'],
                'facebook_id'   => $this->post['id'],
                'created_at'    => $this->post['created_time']->format('Y-m-d H:i:s'),
                'updated_at'    => $this->post['created_time']->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Take the current posts collection and output as transformed array.
     *
     * @return array
     */
    public function output()
    {
        // Loop and transform
        foreach ($this->posts as $key => $post) {
            if (!isset($post['type']) || in_array($post['type'], $this->filteredTypes)) {
                unset($this->posts[$key]);
                continue;
            }

            // Handle if this an array. Will be date object from FB call, but an array from redis
            if (is_array($post['created_time'])) {
                $formattedTime = Carbon::parse($post['created_time']['date']);

                // Overwrite the posts 'created_time' with the parsed above as this is used later for ordering
                $this->posts[$key]['created_time'] = $formattedTime;

                // Set the created_at value to a formatted timestamp of the parsed date above
                $this->posts[$key]['created_at'] = $formattedTime->format('d/m/Y H:i');
            } else {
                // If the code falls into this block, it is already as DateTime object, so just format it
                $this->posts[$key]['created_at'] = $post['created_time']->format('d/m/Y H:i');
            }

            if ($this->isLocallyActive($post['id'])) {
                $this->posts[$key]['status'] = 'Active';
            } else {
                $this->posts[$key]['status'] = 'Inactive';
            }
        }

        // Order by datetime using the above created_time datetime object
        usort($this->posts, function ($a, $b) {
            if ($a['created_time'] == $b['created_time']) {
                return 0;
            }

            return $a['created_time'] > $b['created_time'] ? -1 : 1;
        });

        return $this->posts;
    }

    /**
     * Retrieve a single video by ID from the page.
     *
     * @return array
     */
    public function videoById($id)
    {
        $redisId = 'facebook:videoById:' . $id;

        // Check redis store
        if (Redis::get($redisId)) {
            return $this->post = (array) json_decode(Redis::get($redisId));
        }

        $this->connectFacebookApi();

        try {
            $params = "created_time,source";
            $post = $this->userApi->get('/' . $id . '?fields=' . $params)->getGraphNode()->asArray();
        } catch (FacebookSDKException $e) {
            Log::error($e);
            throw new FitnessPreconditionException('Unable to retrieve video content. Please try again later.');
        }

        // Transform
        $post['created_at'] = $post['created_time']->format('d/m/Y H:i');
        if ($this->isLocallyActive($post['id'])) {
            $post['status'] = 'Active';
        } else {
            $post['status'] = 'Inactive';
        }

        // Set Redis store
        Redis::set($redisId, json_encode($post), 'EX', 3600);

        return $this->post = $post;
    }


    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Work out the content of the post (video link etc.).
     *
     * @return void
     */
    public function getPostContent()
    {
        if (isset($this->post['source']) && $this->post['source'] != '') {
            return $this->post['source'];
        }
        return $this->post['link'];
        // Also possible to return $this->post['source'] for direct link but not for all posts
    }

    /**
     * Work out a title for the post.
     *
     * @return string
     * @throws \Exception
     */
    public function getPostTitle()
    {
        if (!isset($this->post)) {
            throw new FitnessPreconditionException('Error, unable to retrieve Facebook post title.');
        }

        // Check if we have a message within allowed size
        if (strlen($this->post['message']) > 0 && strlen($this->post['message']) < 101) {
            return $this->post['message'];
        }

        // Create a title from a large message
        if (strlen($this->post['message']) > 100) {
            $lines = explode("\n", $this->post['message']);
            if (strlen($lines[0]) < 101) {
                return $lines[0];
            } else {
                return substr($lines[0], 0, 100);
            }
        }

        // Check if we have a story
        if (isset($this->post['story']) && $this->post['story'] != '') {
            return $this->post['story'];
        }
    }

    /**
     * Work out a type for the post.
     *
     * @return string
     */
    public function getPostType()
    {
        switch ($this->post['type']) {
            case 'photo':
                $type = 'image';
                break;
            default:
                $type = $this->post['type'];
                break;
        }

        // Prepend "facebook_" as a differentiator
        return 'facebook_' . $type;
    }
}
