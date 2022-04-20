<?php

namespace Rhf\Modules\Zendesk\Services;

use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Collection;
use Redis;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Zendesk\Requests\ZendeskTicketRequest;
use Throwable;
use Zendesk\API\Exceptions\ApiResponseException;
use Zendesk\API\HttpClient as ZendeskClient;
use Sentry\Laravel\Facade as Sentry;

class ZendeskService
{
    protected $client;
    protected $username;
    protected $token;
    protected $user;
    protected $secret;

    public function __construct()
    {
        $this->token = config('services.zendesk.ZENDESK_API');
        $this->username = config('services.zendesk.ZENDESK_USERNAME');
        $this->secret = config('services.zendesk.ZENDESK_SECRET');
        $this->client = new ZendeskClient(config('services.zendesk.ZENDESK_URL'));

        $this->client->setAuth('basic', [
            'username' => $this->username,
            'token' => $this->token
        ]);
    }

    /**
     * Set User
     *
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * return User
     *
     * @return null
     * @throws Exception
     */
    public function getUser()
    {
        if (!isset($this->user)) {
            throw new Exception('User is not set');
        }
        return $this->user;
    }


    /**
     * Return a JWT String
     *
     * @param $user
     * @return false|string
     */
    public function jwt(User $user)
    {
        $now = time();

        try {
            $token = [
                'jti' => md5($now . rand()),
                'iat' => $now,
                'name' => $user->first_name . ' ' . $user->surname,
                'email' => $user->email,
            ];
            $jwt = JWT::encode($token, $this->secret);
            return $jwt;
        } catch (Throwable $e) {
            Sentry::captureException($e);
            throw new FitnessBadRequestException(
                'Internal: Zendesk Error',
                '422'
            );
        }
    }

    /**
     * Return all zendesk tickets
     *
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function getAllTickets()
    {
        return $this->client->tickets()->findAll();
    }

    /**
     * Create a new Zendesk ticket
     *
     * @param $name
     * @param $email
     * @param $subject
     * @param $body
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function createNewTicket($name, $email, $subject, $body, array $tags = [], $attachments = [])
    {
        try {
            $new = $this->client->tickets()->create([
                'requester' => [
                    'name' => $name,
                    'email' => $email,
                ],
                'subject' => $subject,
                'comment' => [
                    'body' => $body,
                    'uploads' => $attachments,
                ],
                'priority' => 'normal',
                'tags' => $tags
            ]);
            return $new;
        } catch (ApiResponseException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Upload attachments to ZD and get upload Token to attach to tickets
     *
     * @param array $attachment
     * @return array
     * @throws \Zendesk\API\Exceptions\CustomException
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function addAttachmentToTicket(array $attachment): array
    {
        $uploads = [];
        foreach ($attachment as $file) {
            $upload = $this->client->tickets()->attachments()->upload([
                'file' => $file['file'],
                'name' => $file['name']
            ]);
            $uploads[] = $upload->upload->token;
        }
        return $uploads;
    }

    /**
     * Build an array of Attachments to upload to Zendesk
     *
     * @param ZendeskTicketRequest $request
     * @param string $name
     * @return array
     */
    public function buildAttachmentArray(ZendeskTicketRequest $request, string $name): array
    {
        $files = $request->file('files');
        $attachments = [];

        foreach ($files as $file) {
            $filename = str_replace(' ', '-', $name) . '-' . $file->getClientOriginalName();
            $attachments[] = [
                'file' => $file->getPathname(),
                'name' => strtolower($filename),
            ];
        }
        $uploads = $this->addAttachmentToTicket($attachments);
        return $uploads;
    }

    /**
     * Get Ticket for user against users zendesk id
     *
     * @param int $id
     * @return mixed
     */
    public function getUserTickets(int $id)
    {
        return $this->client
            ->users($id)
            ->tickets()
            ->requested();
    }

    /**
     * Return all a users tickets that are not solved/closed
     * and have a matching tag
     *
     * Used to check if a user can make another ticket for the same article
     *
     * @param string $email
     * @param string $tag
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function getOpenTickets(string $email, string $tag)
    {
        return $this->client
            ->search()
            ->find(
                'status<solved requester:' . $email . ' type:ticket tags:' . $tag
            );
    }

    /**
     * Get Ticket by id with comments
     *
     * @param int $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function getTicketById(int $id)
    {
        return $this->client
            ->tickets($id)
            ->comments()
            ->findAll(['sort_order' => 'asc']);
    }

    /**
     * Search for a ticket by ID
     *
     * @param int $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     * @throws \Zendesk\API\Exceptions\RouteException
     */
    public function searchTickets(int $id)
    {
        return $this->client
            ->search()
            ->find(['query' => $id]);
    }

    /**
     * Get all activities on tickets
     *
     * @param int $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function getTicketActivities(int $id)
    {
        return $this->client
            ->activities()
            ->find($id);
    }

    /**
     * Get User details by their Zendesk Id
     *
     * @param int $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function getUserById(int $id)
    {
        return $this->client
            ->users()
            ->find($id);
    }

    /**
     * Update a ticket and add attachments if any
     *
     * @param int $id
     * @param int $zendeskUserId
     * @param string $comment
     * @param array $attachments
     * @return \stdClass|null
     */
    public function updateTicket(int $id, int $zendeskUserId, string $comment, array $attachments = [])
    {
        return $this->client->tickets()->update($id, [
            'comment' => [
                'author_id' => $zendeskUserId,
                'body' => $comment,
                'uploads' => $attachments,
            ]
        ]);
    }

    /**
     * Check if it was the user who last replied to a ticket to prevent spam
     *
     * @param $ticketId
     * @param $zendeskUserId
     * @return bool
     */
    public function userCanReply(int $ticketId, int $zendeskUserId): bool
    {
        $comments = collect($this->getTicketById($ticketId)->comments);
        $lastComment = $comments->last();
        return $lastComment->author_id !== $zendeskUserId;
    }

    /**
     * Returns Zendesk User Object based on user email query
     *
     * @param $email
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function getUserByEmail(string $email)
    {
        return $this->client->users()->search(['query' => $email]);
    }

    /**
     * Return all articles for zendesk account
     *
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function articles()
    {
        return $this->client->helpCenter->articles()->findAll();
    }

    /**
     * Return article by id
     *
     * @param $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function article(int $id)
    {
        return $this->client->helpCenter->articles()->find($id);
    }

    /**
     * Return all categories for zendesk account
     *
     * @return \stdClass|null
     * @throws ApiResponseException
     * @throws \Zendesk\API\Exceptions\AuthException
     */
    public function categories()
    {
        return $this->client->helpCenter->categories()->findAll();
    }

    /**
     * Return category by id
     *
     * @param $id
     * @return \stdClass|null
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function category(int $id)
    {
        return $this->client->helpCenter->categories()->find(['id' => $id]);
    }

    /**
     * Add an Internal note to a ticket
     *
     * @param $id
     * @param string $note
     */
    public function internalNote(int $id, string $note)
    {
        $this->client->tickets()->update($id, [
            'comment' => [
                'body' => $note,
                'public' => 'false'
            ],
        ]);
    }

    /**
     * Check if User Has open tickets
     *
     * @param User $user
     * @param $tag
     */
    public function userHasOpenTicket(User $user, string $tag)
    {
        $tickets = $this->getOpenTickets($user->email, $tag);
        $results = collect($tickets->results)->last();
        return $results;
    }

    /**
     * Organises and groups the users tickets by Week
     * returns a collection based on the values of the output
     *
     * @param Collection $tickets
     * @return Collection
     */
    public function organiseUserTickets(Collection $tickets): Collection
    {
        return $tickets
            ->reverse()
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('YW');
            })
            ->map(function ($tickets) {
                return [
                    'period_start' => iso_date($tickets[0]->created_at),
                    'tickets' => $this->processUsersTickets($tickets)
                ];
            })
            ->values();
    }

    /**
     * @param int $ticketId
     * @param int $zendeskUserId
     * @return array
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function hasNewReply(int $ticketId, int $zendeskUserId): array
    {
        $comments = collect($this->getTicketById($ticketId)->comments);
        $lastComment = clone ($comments)->last();
        $unread = $lastComment->author_id === $zendeskUserId ? false : true;

        $unreadCount = array_search(
            $zendeskUserId,
            array_column($comments->reverse()->toArray(), 'author_id')
        );

        return [
            'unread' => $unread,
            'unreadCount' => $unreadCount
        ];
    }

    /**
     * Checks if the user has a cached item
     * If they do check the time against the limit offered and returns
     * also checks if null and returns the cache, else returns false
     *
     * @param string $key
     * @param null $limit
     * @return false|mixed
     */
    public function checkUserCache(string $key, $limit = null)
    {
        if ($this->isCached($key)) {
            $cache = $this->getCache($key);
            $lastAccessed = Carbon::parse($cache->accessed);
            if (is_null($limit) || $lastAccessed->diffInMinutes(now()) <= $limit) {
                return $cache;
            }
        }
        return false;
    }

    /**
     * Checks if the key exists in the cache
     *
     * @param $key
     * @return bool
     */
    public function isCached($key): bool
    {
        return !is_null(Redis::get($key)) ? true : false ;
    }

    /**
     * return the item from the cache if the key exists
     * @param $key
     * @return mixed
     */
    public function getCache($key)
    {
        return json_decode(Redis::get($key));
    }

    /**
     * Adds items to cache based on specific key
     *
     * @param string $key
     * @param array $value
     */
    public function addToCache(string $key, array $value, $limit = null)
    {
        $value = json_encode($value);
        if (is_null($limit)) {
            return Redis::set($key, $value);
        }
        return Redis::setex($key, $limit, $value);
    }

    /**
     * Delete key from cache
     *
     * @param string $key
     * @return int
     */
    public function delCache(string $key)
    {
        return Redis::del($key);
    }

    /**
     * Process a users tickets and convert them to a readable format
     * check if the ticket is open if it has had replies and how many
     * @param Collection $tickets
     * @return array
     */
    private function processUsersTickets(Collection $tickets): array
    {
        return $tickets->map(function ($ticket) {
            $unread = false;
            $unreadCount = 0;

            if ($ticket->status == 'open') {
                $replies = $this->hasNewReply($ticket->id, $ticket->requester_id);
                $unread = $replies['unread'];
                $unreadCount = $replies['unreadCount'];
            }

            return [
                'ticket' => [
                    'id' => $ticket->id,
                    'contents' => $ticket->description,
                    'time' => Carbon::parse($ticket->created_at)->format('G:i:s'),
                    'status' => $ticket->status
                ],
                'unread' => $unread,
                'unread_count' => $unreadCount,
            ];
        })
            ->toArray();
    }
}
