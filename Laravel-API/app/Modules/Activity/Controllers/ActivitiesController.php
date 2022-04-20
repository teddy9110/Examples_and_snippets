<?php

namespace Rhf\Modules\Activity\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Activity\Models\ActivityNotes;
use Rhf\Modules\Activity\Filters\ActivityFilter;
use Rhf\Modules\Activity\Services\ActivityCache;
use Rhf\Modules\Activity\Requests\ActivitiesRequest;
use Rhf\Modules\Activity\Services\ActivitiesService;
use Rhf\Modules\System\Resources\PaginationResource;
use Rhf\Modules\Activity\Requests\ActivityLogRequest;
use Rhf\Modules\Activity\Resources\ActivitiesResource;
use Rhf\Modules\Activity\Services\ActivityNoteService;

class ActivitiesController extends Controller
{
    protected $activityNoteService;
    protected $activitiesService;

    /**
     * Create a new ActivityController instance.
     *
     * @return void
     */
    public function __construct(
        ActivityNoteService $activityNoteService,
        ActivitiesService $activitiesService
    ) {
        $this->activityNoteService = $activityNoteService;
        $this->activitiesService = $activitiesService;
    }

    /**
     * Create an activity based on request
     * Create Cache if part of caching system
     * Create note if part of caching system
     *
     * @param ActivitiesRequest $request
     * @return JsonResponse
     */
    public function createActivity(ActivitiesRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->user()->id;

        $activity = $this->activitiesService->createActivity($data);
        // Checks if it should be notable
        $this->handleNotes($activity, $request, $data['date']);

        return response()->json(['status' => 'success', 'data' => $activity]);
    }

    /**
     * Update Activity via id
     * Clear cache and update
     *
     * @param $id
     * @param ActivitiesRequest $request
     */
    public function updateActivity($id, ActivitiesRequest $request)
    {
        $data = $request->validated();
        $data['activity_id'] = $id;

        $activity = $this->activitiesService->getActivity($id);
        // Handle Existing Activity
        $exists = $this->activitiesService->getActivityByTypeAndDate($activity->type, $data['date']);
        if ($exists && $activity->date != $exists->date) {
            $this->deleteExistingActivity($exists);
        }
        // Update Activity
        $updated = $this->activitiesService->updateActivity($activity, $data);

        $this->handleExistingNotes($updated, $request, $updated->date);
        return new ActivitiesResource($updated);
    }

    /**
     * @param ActivityLogRequest $request
     * @param int|null $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|ActivitiesResource
     */
    public function getActivity(int $id)
    {
        $activity = $this->activitiesService->getActivity($id);
        return new ActivitiesResource($activity);
    }

    /**
     * @param ActivityLogRequest $request
     */
    public function getActivities(ActivityLogRequest $request)
    {
        $filters = $request->validated();
        $filters['range']['start_date'] = $request->input('start_date', null);
        $filters['range']['end_date'] = $request->input('end_date', null);
        $filters['order']['sort_by'] = $request->input('sort_by', null);
        $filters['order']['sort_direction'] = $request->input('sort_direction', null);

        $pagination = [];
        // Non-grouped response - returns a simple paginated list of activities.
        // IMPORTANT: Different response structure.
        if (!$request->has('group_by')) {
            if ($request->has('page')) {
                $pagination['page'] = intval($request->input('page'));
                $pagination['per_page'] = $request->input('limit') == 0 ? 10 : intval($request->input('limit', 10));
            }
            $activity = $this->handleDiaryCaching($request, $filters);
            return ActivitiesResource::collection($activity);
        }

        $pagination['page'] = intval($request->input('page', 1));
        $pagination['per_page'] = $request->input('limit') == 0 ? 10 : intval($request->input('limit', 10));

        $period = $request->input('group_by', 'week');
        $sortDirection = $request->input('sort_direction', 'desc');

        $startDate = $request->has('start_date') ?
            Carbon::parse($request->input('start_date')) :
            auth('api')->user()->created_at;

        $endDate = $request->has('end_date') ?
            Carbon::parse($request->input('end_date')) :
            Carbon::now();

        $totalResults = iterator_count(get_period_range($startDate, $endDate, $period));

        if ($pagination['page'] > ceil($totalResults / $pagination['per_page'])) {
            $data = paginate([], $totalResults, $pagination['per_page'], $pagination['page']);
            return new PaginationResource($data);
        }

        $dateRange = get_pagination_period_range(
            $pagination['page'],
            $pagination['per_page'],
            $startDate,
            $endDate,
            $period,
            $sortDirection
        );

        $filters['range']['start_date'] = $dateRange->start;
        $filters['range']['end_date'] = $dateRange->end;

        $activity = $this->handleDiaryCaching($request, $filters, $period);

        $grouped = $this->activitiesService->activitiesGroupByDate(
            $activity,
            $dateRange,
            $period,
            $sortDirection
        );

        $data = paginate($grouped, $totalResults, $pagination['per_page'], $pagination['page']);
        return new PaginationResource($data);
    }

    /**
     * Retrieve Activity via ID
     * Clear Cache for activity/date
     * Delete Activity, expects Activity Model
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteActivity($id)
    {
        $activity = $this->activitiesService->getActivity($id);
        $this->activitiesService->deleteActivity($activity);
        return response()->noContent();
    }

    /**
     * @param $activity
     * @param ActivitiesRequest $request
     * @param $date
     */
    private function handleNotes($activity, ActivitiesRequest $request, $date): void
    {
        //Checks if should be notable
        $details = array();
        if ($request->has('details.note')) {
            $details['note'] = $request->input('details.note');
        }
        if ($request->has('details.period')) {
            $details['period'] = $request->input('details.period');
        }
        if ($request->has('details.body_fat_percentage')) {
            $details['body_fat_percentage'] = $request->input('details.body_fat_percentage');
        }

        $notable = $this->activityNoteService->shouldNote(
            $activity->type,
            $details
        );

        if ($notable) {
            $this->activityNoteService->createNote(
                $activity->id,
                $date,
                $details
            );
        }
    }

    /**
     * @param $exists
     * @throws Exception
     */
    private function deleteExistingActivity($exists): void
    {
        $this->activitiesService->deleteActivity($exists);
    }

    /**
     * Handle existing notes
     *
     * @param $activity
     * @param $request
     * @param $date
     */
    private function handleExistingNotes($activity, $request, $date)
    {
        $noteExists = ActivityNotes::where('activity_id', $activity->id)->first();
        if (
            $noteExists &&
            is_null($request->input('details.note')) &&
            $request->input('details.period') === 'false'
        ) {
            $noteExists->delete();
        } else {
            $this->handleNotes($activity, $request, $date);
        }
    }

    /**
     * Handle the caching of long term activities such as Diary/etc
     *
     * @param ActivityLogRequest $request
     * @param array $filters
     * @param array $pagination
     * @return bool|mixed
     */
    private function handleDiaryCaching(
        ActivityLogRequest $request,
        array $filters,
        string $period = null
    ) {
        $type = $request->input('type', 'all');
        $activity = $this->activitiesService->getActivities(new ActivityFilter($filters));
        return $activity;

        //TODO: Look into why caching is removing a day

        // $key = $this->generateCacheKey($request, $period, $filters['range']);
        // $activityCache = new ActivityCache(auth('api')->user());
        // $cachedActivities = $activityCache->getCache($key);
        // if (!is_null($cachedActivities)) {
        //     return collect(json_decode($cachedActivities));
        // }
        // $activity = $this->activitiesService->getActivities(new ActivityFilter($filters));
        // return $activity;
        // if ($activity->isNotEmpty() && $activityCache->isCacheableActivity($type)) {
        //     $activityCache->setCache($key, $activity, ActivityCache::$expiryTtl);
        //     return $activity;
        // }
        // return $activity;
    }

    /**
     * @param ActivityLogRequest $request
     * @param $type
     * @param string|null $period
     * @param $range
     * @return string
     */
    private function generateCacheKey(ActivityLogRequest $request, ?string $period, $range): string
    {
        $activityCache = new ActivityCache(auth('api')->user());

        if (!$request->has('group_by')) {
            return $activityCache->createPeriodCacheKey(
                'long',
                $request->input('type'),
                Carbon::parse($request->input('start_date', auth('api')->user()->created_at)),
                Carbon::parse($request->input('end_date', now()->format('Y-m-d')))
            );
        } else {
            return $activityCache->createPeriodCacheKey(
                $period,
                $request->input('type'),
                Carbon::parse($range['start_date']),
                Carbon::parse($range['end_date'])
            );
        }
    }
}
