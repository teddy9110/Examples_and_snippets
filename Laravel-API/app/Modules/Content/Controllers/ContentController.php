<?php

namespace Rhf\Modules\Content\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\System\Services\FacebookExtender as Facebook;
use Rhf\Modules\Content\Resources\CategoryResource;
use Rhf\Modules\Content\Resources\ContentResource;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Models\Category;
use Rhf\Modules\Content\Services\FacebookContentService;

class ContentController extends Controller
{
    private $fb; // Facebook service
    private $appApi; // The facebook api class for the app
    private $userApi; // The facebook api class for the user
    private $pageApi; // The facebook api class for the page

    /**
     * @var Content
     */
    protected $contentModel;

    /**
     * Create a new ExerciseController instance.
     *
     * @param Facebook $fb
     * @param Content $content
     */
    public function __construct(Facebook $fb, Content $content)
    {
        $this->fb = $fb;
        $this->contentModel = $content;
    }

    /**
     * Get the available content.
     *
     * @param (int) categoryId
     * @param (int) contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $categoryId = null, $contentId = null)
    {
        try {
            if (isset($contentId)) {
                return $this->getContent($contentId);
            } elseif (isset($categoryId)) {
                return $this->getCategory($categoryId);
            } else {
                // Categories we do not want to send down in the video library
                $categoriesIgnored = ['life-plan', 'progress-pictures'];

                $categories = Category::where('parent_id', '=', 0)
                    // Filter out the new life plan videos with the slug life-plan
                    ->whereNotIn('slug', $categoriesIgnored)
                    ->with(['parent','allChildren'])
                    ->get();
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve content.');
        }

        return response()->json(['status' => 'success', 'data' => CategoryResource::collection($categories)]);
    }

    /**
     * Get the provided category.
     *
     * @param (int) categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategory($categoryId)
    {
        try {
            $categoryResource = new CategoryResource(Category::find($categoryId));
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve content.');
        }

        return response()->json(['status' => 'success', 'data' => $categoryResource]);
    }

    /**
     * Get the provided category.
     *
     * @param (int) contentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContent($contentId)
    {
        try {
            $contentResource = new ContentResource(Content::find($contentId));
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve content.');
        }

        return response()->json(['status' => 'success', 'data' => $contentResource]);
    }

    /**
     * Retrieve a video URL from facebook as JSON.
     *
     * @param (int) video id
     * @return \Illuminate\Http\Response
     */
    public function getVideoUrl(Request $request, $id)
    {
        try {
            // FIXME: Temporary workaround for intro video
            if ($id == '611062542642812') {
                $post = [
                    'source' =>
                        'https://teamrh-prod.ams3.cdn.digitaloceanspaces.com/prod/facebook-videos/login_screen.mp4',
                ];
            } else {
                $facebookContentService = new FacebookContentService();
                $post = $facebookContentService->videoById($id);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve video.');
        }

        return response()->json(['status' => 'success', 'data' => [
            'url' => $post['source'],
        ]]);
    }

    /**
     * Get content by search term.
     *
     * @param (string) term
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $term)
    {
        try {
            $contentResource = ContentResource::collection($this->contentModel->search(['value' => $term])->get());
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Unable to retrieve content.');
        }

        return response()->json(['status' => 'success', 'data' => $contentResource]);
    }

    /**
     * Retrieve all content videos that are not hosted on Facebook
     *
     * @param Request $request
     * @param $slug
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function retrieveContentVideos(Request $request, $slug)
    {
        $contentVideos = $this->contentModel
            ->load('category')
            ->whereHas('category', function ($query) use ($slug) {
                // Filter the query down to only categories with the slug passed in
                $query->where('slug', '=', $slug);
            })
            ->orderBy('order', 'ASC')
            ->get();

        return ContentResource::collection($contentVideos);
    }
}
