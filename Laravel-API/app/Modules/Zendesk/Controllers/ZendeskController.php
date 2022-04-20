<?php

namespace Rhf\Modules\Zendesk\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Content\Resources\CategoryResource;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\Zendesk\Requests\ZendeskTicketRequest;
use Rhf\Modules\Zendesk\Resources\CategoriesResource;
use Rhf\Modules\Zendesk\Resources\TicketResource;
use Rhf\Modules\Zendesk\Services\ZendeskService;

class ZendeskController extends Controller
{
    protected $zendeskService;
    protected $ticketListTtl;
    protected $ticketCacheTtl;

    public function __construct()
    {
        $this->zendeskService = new ZendeskService();
        $this->ticketListTtl = config('app.zendesk_ticket_list_cache_ttl');
        $this->ticketCacheTtl = config('app.zendesk_ticket_cache_ttl');
    }

    /**
     * Create and return zendesk JWT
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $request->validate([
            'user_token' => 'required|exists:users,id'
        ]);

        $id = $request->input('user_token');
        $user = User::findOrFail($id);
        $jwt = $this->zendeskService->jwt($user);
        return response()->json(['jwt' => $jwt]);
    }


    /**
     * Return article ID
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function article(Request $request, string $slug = null)
    {
        try {
            $slug = $slug ?? $request->input('article_slug');
            $id = config('app.zendesk_' . $slug);
            $article = $this->zendeskService->article($id);
            if ($request->isMethod('post')) {
                return response()->json([
                    'url' => $article->article->html_url,
                ], 200);
            }
            return  response()->json([
                'data' => [
                    'url' => $article->article->html_url,
                ]
            ], 200);
        } catch (Exception $e) {
            throw new Exception('Sorry, we are unable to retrieve the article you have requested. Please try again.');
        }
    }

    /**
     * Check if a user has open tickets based on article_id tag
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hasOpenTicket(Request $request)
    {
        $tag = $request->input('tag');
        $user = auth('api')->user();
        $hasOpenTicket = $this->zendeskService->userHasOpenTicket($user, $tag);
        $ticketId = is_null($hasOpenTicket) ? null : $hasOpenTicket->id;
        return response()->json([
            'data' => [
                'ticket_id' => $ticketId
            ]
        ], 200);
    }

    /**
     * Checks Zendesk cache / tickets for any with an unread status
     * and returns true/false boolean.  Need to check for collection using
     * instanceOf in order to prevent data issues with values being
     * retrieved from cache since they are objects.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function unread()
    {
        $zendeskUserId = $this->getZendeskUserId();
        if (!$zendeskUserId) {
            throw new FitnessHttpException(
                'Sorry, Unable to process that request. You have not created any support tickets.',
                400
            );
        }
        $key = 'zendesk_user_id:' . $zendeskUserId;
        $userTickets = $this->zendeskService->checkUserCache($key);
        if (!$userTickets) {
            $tickets = collect($this->zendeskService->getUserTickets($zendeskUserId)->tickets);
            $userTickets = $this->zendeskService->organiseUserTickets($tickets);
        }
        $ticketData = $userTickets instanceof Collection ? $userTickets : collect($userTickets->data);
        // Loops over the collection and checks if result contains true;
        $unread = $ticketData->map(function ($item) {
            $tickets = is_array($item) ? collect($item['tickets']) : collect($item->tickets);
            $unread = $tickets->where('unread', '=', true);
            return $unread->isNotEmpty();
        })
        ->contains(true);

        return response()->json([
            'data' => [
                'unread' => $unread
            ]
        ]);
    }

    /**
     * Return all of a users tickets
     * grouped by date and in DESC order
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function usersTickets()
    {
        $zendeskUserId = $this->getZendeskUserId();
        if (!$zendeskUserId) {
            throw new FitnessHttpException(
                'Sorry, Unable to process that request. You have not created any support tickets.',
                400
            );
        }

        $key = 'zendesk_user_id:' . $zendeskUserId;

        $userTickets = $this->zendeskService->checkUserCache($key, $this->ticketListTtl);
        if (!$userTickets) {
            $tickets = collect($this->zendeskService->getUserTickets($zendeskUserId)->tickets);
            $userTickets = $this->zendeskService->organiseUserTickets($tickets);

            $userData = [
                'data' => $userTickets->toArray(),
                'accessed' => now()
            ];
            $this->zendeskService->addToCache($key, $userData);
        } else {
            $userTickets = $userTickets->data;
        }
        return response()->json([
            'data' => $userTickets
        ], 200);
    }

    /**
     * Get a Ticket by ID, scoped to comment only
     * If a ticket is accessed updates the cached if present marking that ticket
     * as unread and resets count to 0
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Zendesk\API\Exceptions\MissingParametersException
     */
    public function getTicket($id)
    {
        $key = 'zendesk_user_id:' . $this->getZendeskUserId();

        if (!$this->getZendeskUserId()) {
            throw new FitnessHttpException(
                'Sorry, Unable to process that request. You have not created any support tickets.',
                400
            );
        }

        $userTickets = $this->zendeskService->checkUserCache($key, $this->ticketListTtl);
        if ($userTickets) {
            $cache = $userTickets;
            $cachedCollection = collect($userTickets->data);
            $cachedCollection->each(function ($item) use ($id) {
                $search = collect($item->tickets)
                    ->filter(function ($item, $key) use ($id) {
                        $ticketArray = (array) $item->ticket;
                        return $ticketArray['id'] == $id;
                    })
                    ->keys()
                    ->all();
                $key = last($search);
                $ticket = $item->tickets[$key];
                $ticket->unread = false;
                $ticket->unread_count = 0;
            });
            //Update cache
            $cache->accessed = now();
            $this->zendeskService->addToCache($key, collect($cache)->toArray());
        }
        $ticketKey = 'zendesk_ticket_id:' . $id;
        $ticketCached = $this->zendeskService->isCached($ticketKey);

        if ($ticketCached) {
            $ticket = $this->zendeskService->getCache($ticketKey);
            $comments = collect($ticket);
            return TicketResource::collection($comments);
        }
        $ticket = collect($this->zendeskService->getTicketById($id)->comments);
        $this->zendeskService->addToCache($ticketKey, $ticket->reverse()->toArray(), $this->ticketCacheTtl);
        return TicketResource::collection($ticket->reverse());
    }

    /**
     * Create a Ticket for a user
     *
     * @param ZendeskTicketRequest $request
     */
    public function createTicket(ZendeskTicketRequest $request)
    {
        $comment = $request->input('comment');
        $tags = $request->input('tags');

        $user = Auth::user();
        $uploads = [];

        $tags[] = $request->input('platform');
        $tags[] = $request->input('app_version');

        if ($request->has('files')) {
            $uploads = $this->zendeskService->buildAttachmentArray($request, $user->name);
        }

        $createTicket = $this->zendeskService->createNewTicket(
            $user->name,
            $user->email,
            'Chat to Coaches',
            $comment,
            $tags,
            $uploads
        );

        if ($createTicket->ticket->id) {
            $key = 'zendesk_user_id:' . $this->getZendeskUserId();
            $this->zendeskService->delCache($key);
            $ticket = collect($this->zendeskService->getTicketById($createTicket->ticket->id)->comments);
            return TicketResource::collection($ticket);
        }
        return new FitnessHttpException('Sorry, you are unable to comment at this time', 400);
    }

    /**
     * update a users ticket as the user
     *
     * @param ZendeskTicketRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    public function updateTicket(ZendeskTicketRequest $request, $id)
    {
        $user = Auth::user();
        $zendeskUserId = $this->getZendeskUserId();

        if ($this->zendeskService->userCanReply($id, $zendeskUserId)) {
            $comment = $request->input('comment');
            $uploads = [];
            if ($request->has('files')) {
                $uploads = $this->zendeskService->buildAttachmentArray($request, $user->name);
            }

            $updateTicket = $this->zendeskService->updateTicket($id, $zendeskUserId, $comment, $uploads);
            if ($updateTicket->ticket->id) {
                //delete cache
                $ticketKey = 'zendesk_ticket_id:' . $id;
                $this->zendeskService->delCache($ticketKey);
                $ticket = collect($this->zendeskService->getTicketById($updateTicket->ticket->id)->comments);
                return TicketResource::collection($ticket);
            }
        } else {
            throw new FitnessHttpException('Sorry, you are unable to comment at this time', 400);
        }
    }

    /**
     * @return mixed
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    private function getZendeskUserId()
    {
        $user = Auth::user();
        $zendeskUser = $this->zendeskService->getUserByEmail($user->email);
        if (empty($zendeskUser->users)) {
            return false;
        }
        $zendeskUserId = $zendeskUser->users[0]->id;
        return $zendeskUserId;
    }
}
