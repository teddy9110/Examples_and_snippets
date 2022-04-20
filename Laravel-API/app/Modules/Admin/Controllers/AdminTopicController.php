<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Resources\AdminSubtopicResource;
use Rhf\Modules\Admin\Resources\AdminTopicResource;
use Rhf\Modules\Notifications\Models\SubTopics;
use Rhf\Modules\Notifications\Models\Topics;
use Rhf\Modules\Notifications\Services\NotificationService;
use Rhf\Modules\Notifications\Services\TopicService;

class AdminTopicController extends Controller
{
    /**
     * @var TopicService
     */
    private $topicService;
    private $notificationService;

    /**
     * AdminTopicController constructor.
     * @param TopicService $topicService
     */
    public function __construct(TopicService $topicService, NotificationService $notificationService)
    {
        $this->topicService = $topicService;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filter = Arr::wrap($request->input('filter', []));

        $query = Topics::query()->with('subtopics')->orderBy($orderBy, $orderDirection);

        return AdminTopicResource::collection($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|string',
            'description' => 'string',
        ]);

        try {
            $topic = Topics::create([
                'category' => $request->input('title'),
                'description' => $request->input('description')
            ]);

            $data = [
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'slug' => Str::slug(strtolower($request->input('title')), '_'),
                'topic_id' => $topic->id,
                'active' => 0,
                'subscribe' => 0,
            ];
            (new SubTopics())->createSubtopicAndSubscribeAll($data);

            return new AdminTopicResource($topic);
        } catch (\Exception $e) {
            throw new FitnessHttpException('Error creating topic', 500);
        }
    }

    public function createSubTopic(Request $request)
    {
        $validate = $request->validate([
            'id' => 'integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'topic_id' => 'required|exists:topics,id',
            'active' => 'boolean',
            'subscribe' => 'boolean'
        ]);

        try {
            $subtopic = new SubTopics();
            $exists = $subtopic->where('id', $request->id)->first();

            // need to get topic category
            // and make slug
            $topic = Topics::findOrFail($request->topic_id)->category;
            $validate['slug'] = Str::slug(strtolower($topic) . "_" . $validate['title'], '_');

            if ($exists) {
                $subtopic->updateSubtopicAndSubscribeAll($validate);
                return new AdminSubtopicResource($exists);
            } else {
                $topic = $subtopic->createSubtopicAndSubscribeAll($validate);
                return new AdminSubtopicResource($topic);
            }
        } catch (\Exception $e) {
            throw new FitnessHttpException('Error creating topic', 500);
        }
    }

    public function subtopics(Request $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filter = Arr::wrap($request->input('filter', []));

        $query = SubTopics::query()->with('topic')->where('active', 1)->orderBy($orderBy, $orderDirection);
        return AdminSubtopicResource::collection($query->paginate($perPage));
    }

    public function show(Topics $id)
    {
        return new AdminTopicResource($id);
    }

    public function showSubtopic(SubTopics $id)
    {
        return new AdminSubtopicResource($id);
    }

    public function destroy($id)
    {
        try {
            $subtopic = new SubTopics();
            $subtopic->remove($id);

            return response()->json([
                'message' => 'Topic deleted'
            ], 200);
        } catch (Exception $e) {
            throw new Exception('Unable to complete request', $e->getCode());
        }
    }
}
