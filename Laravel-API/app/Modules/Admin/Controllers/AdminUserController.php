<?php

namespace Rhf\Modules\Admin\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminStaffNoteRequest;
use Rhf\Modules\Admin\Requests\AdminUserGraphRequest;
use Rhf\Modules\Admin\Requests\AdminUserRequest;
use Rhf\Modules\Admin\Resources\AdminStaffNoteResource;
use Rhf\Modules\Admin\Resources\AdminUserDetailedResource;
use Rhf\Modules\Admin\Resources\AdminUserGraphResource;
use Rhf\Modules\Admin\Resources\AdminUserExerciseGraphResource;
use Rhf\Modules\Admin\Resources\AdminUserPermissionsResource;
use Rhf\Modules\Admin\Resources\AdminUserProgressResource;
use Rhf\Modules\Admin\Resources\AdminUserResource;
use Rhf\Modules\System\Exceptions\DisplayedErrorException;
use Rhf\Modules\System\Models\ActivityLog;
use Rhf\Modules\User\Models\StaffNote;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserProgressPicture;
use Rhf\Modules\User\Models\UserRole;
use Rhf\Modules\Admin\Services\AdminUserService;
use Rhf\Modules\User\Services\UserFileService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Rhf\Modules\Activity\Models\Activity;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\Admin\Requests\AdminUserDownloadRequest;
use Rhf\Modules\Exercise\Models\Exercise;
use Rhf\Modules\Notifications\Actions\NotificationActions;
use Rhf\Modules\User\Controllers\UserAppStoreReviewController;
use Rhf\Modules\User\Resources\UserAppStoreReviewResource;
use Rhf\Modules\User\Services\UserAppStoreReviewService;
use stdClass;

class AdminUserController extends Controller
{
    private $userService;
    protected $fieldMap = [
        'name' => 'first_name',
        'has_paid' => 'paid',
    ];

    public function __construct(AdminUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get paginated users
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'asc');
        $filterBy = $request->get('filter_by');
        $filterValue = $request->get('filter');
        $filterByAccountType = $request->get('filter_account_type');

        $startDate = $request->get('startDate') != null ? new Carbon($request->get('startDate')) : null;
        $endDate = $request->get('endDate') != null ? new Carbon($request->get('endDate')) : null;
        $type = $request->get('type') == 'expired' ? 'expiry_date' : 'created_at';

        $query = User::query()->withTrashed();

        if ($orderBy == 'name') {
            $query
                ->orderBy('first_name', $orderDirection)
                ->orderBy('surname', $orderDirection);
        } else {
            $mappedOrderBy = isset($this->fieldMap[$orderBy]) ? $this->fieldMap[$orderBy] : $orderBy;
            $query->orderBy($mappedOrderBy, $orderDirection);
        }

        if ($filterBy === 'name') {
            $query->whereRaw("CONCAT(first_name, ' ', surname) LIKE '%$filterValue%'");
        } elseif (isset($filterBy) && isset($filterValue)) {
            $mappedFilterBy = isset($this->fieldMap[$filterBy]) ? $this->fieldMap[$filterBy] : $filterBy;
            $query->where($mappedFilterBy, 'like', "%$filterValue%");
        }

        if ($startDate != null && $endDate != null) {
            $query->customer()
                ->whereBetween($type, [
                    $startDate, $endDate
                ]);
        }

        if ($filterByAccountType == 'staff') {
            $query->staffAccount();
        } elseif ($filterByAccountType == 'customer') {
            $query->customerAccount();
        }

        $showAll = in_array(strtolower($request->input('show_all')), ['1', 'true']);

        return AdminUserResource::collection($showAll ? $query->get() : $query->paginate($perPage));
    }

    public function resetReviewTime()
    {
        $userAppStoreReviewService = new UserAppStoreReviewService(Auth::user());
        $userReview = $userAppStoreReviewService->getUserAppStoreReview();
        $userReview->next_review_request = Carbon::now()->addMinutes(1);
        return new UserAppStoreReviewResource($userReview);
    }

    /**
     * Get specified users
     * @param $id
     * @return AdminUserDetailedResource
     */
    public function show($id)
    {
        return new AdminUserDetailedResource(User::findOrFail($id));
    }

    /**
     * Create a new user
     * @param AdminUserRequest $request
     * @return AdminUserDetailedResource
     * @throws DisplayedErrorException
     */
    public function store(AdminUserRequest $request)
    {
        $user = $this->userService->create($request->json());
        return new AdminUserDetailedResource($user);
    }

    /**
     * Update a user
     * @param AdminUserRequest $request
     * @return AdminUserDetailedResource
     * @throws DisplayedErrorException
     */
    public function update(AdminUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->userService->setUser($user);
        $this->userService->update($request->json());
        return new AdminUserDetailedResource($user);
    }

    /**
     * Soft delete the specified user
     * @param $id
     * @return ResponseFactory|Response
     */
    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return response(null, 204);
    }

    /**
     * Soft delete the specified user
     * @param $id
     * @return ResponseFactory|Response
     */
    public function purge($id)
    {
        $user = User::findOrFail($id);
        $user->preferences()->delete();
        $user->forceDelete();
        return response(null, 204);
    }

    /**
     * Soft delete the specified user
     * @param $id
     * @return ResponseFactory|Response
     */
    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
        return response(null, 204);
    }

    /**
     * Get the specified users graph
     * @param AdminUserGraphRequest $request
     * @param $id
     * @param string $type
     * @return AnonymousResourceCollection
     */
    public function showGraph(AdminUserGraphRequest $request, $id, $type)
    {
        $from = Carbon::parse($request->get('from', Carbon::now()->subWeek(1)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->get('to', Carbon::now()->toDateString()))->endOfDay();
        if ($type == 'exercise') {
            return $this->showExcerciseGraph($from, $to, $id);
        }

        $activities = Activity::query()
            ->with('notes')
            ->where('user_id', $id)
            ->where('type', $type)
            ->where('value', '>', '0')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->date->unix();
            })
            ->map(function ($item) {
                return $item[0];
            });

        return AdminUserGraphResource::collection($activities);
    }

    public function showExcerciseGraph($from, $to, $userId)
    {
        $activities = Activity::with('notes')
            ->where('user_id', $userId)
            ->where('type', 'exercise')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $oldRestExercise = Exercise::whereRaw('lower(title) = ?', ['rest'])->first();
        $activityPeriod = new CarbonPeriod($from, '1 days', $to);

        $graphData = collect($activityPeriod)->map(function ($period) use ($activities, $oldRestExercise) {
            $activity = new stdClass();
            $activity->date = $period;

            $periodActivities = $activities->where('date', $period);
            if ($periodActivities->count() == 0) {
                $activity->value = 0;
            } elseif ($periodActivities->count() == 1) {
                $activity->value = $this->getExerciseActivityValue($periodActivities->first()->value, $oldRestExercise);
            } else {
                $activity->value = $periodActivities->pluck('value')->search(function ($value) use ($oldRestExercise) {
                    return $this->getExerciseActivityValue($value, $oldRestExercise);
                }) !== false ? 1 : 0;
            }
            return $activity;
        });
        return AdminUserExerciseGraphResource::collection($graphData);
    }

    /**
     * Helper to get the activity value.
     * @param mixed $activityValue
     * @param mixed|null $oldRestExercise
     * @return int
     */
    private function getExerciseActivityValue($value, $oldRestExercise = null)
    {
        $isRestExercise = $value == -1 || $value == 0;
        if (!is_null($oldRestExercise) && !$isRestExercise) {
            $isRestExercise = $value != $oldRestExercise->id;
        }
        return $isRestExercise ? 0 : 1;
    }

    /**
     * Get achievements for a user
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function showAchievements($id)
    {
        $this->userService->setUser(User::findOrFail($id));
        return response($this->userService->calculateWeeklyAchievement(3));
    }

    /**
     * Get the specified users paginated staff notes
     * @param Request $request
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function showStaffNotes(Request $request, $id)
    {
        $perPage = intval($request->get('per_page', 20));
        $staffNotes = User::findOrFail($id)
            ->staffNotes()
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        return AdminStaffNoteResource::collection($staffNotes);
    }

    /**
     * Get the specified users paginated progress with pictures
     * @param Request $request
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function showProgress(Request $request, $id)
    {
        $perPage = intval($request->get('per_page', 20));

        $progress = User::findOrFail($id)
            ->progress()
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        return AdminUserProgressResource::collection($progress);
    }

    public function getUserNotes($userId)
    {
        $notes = StaffNote::where('user_id', $userId)->get();
        return AdminStaffNoteResource::collection($notes);
    }

    /**
     * Create a staff note for the specified user
     * @param $id
     * @param AdminStaffNoteRequest $request
     * @return AnonymousResourceCollection
     */
    public function createStaffNote(AdminStaffNoteRequest $request, $id)
    {
        User::findOrFail($id)
            ->staffNotes()
            ->create([
                'note' => $request->get('note'),
                'logged_by' => Auth::id()
            ]);

        return response(null, 201);
    }

    /**
     * Delete a staff note by id
     * @param string $noteId
     * @return ResponseFactory|Response
     */
    public function deleteStaffNote(string $noteId)
    {
        StaffNote::findOrFail($noteId)->delete();
        return response(null, 204);
    }

    /**
     * Update a staff note by id
     * @param string $noteId
     * @return ResponseFactory|Response
     */
    public function updateStaffNote(string $noteId, AdminStaffNoteRequest $request)
    {
        $note = StaffNote::findOrFail($noteId);
        $note->note = $request->note;
        $note->last_updated_by = Auth::id();
        $note->save();
        return response(null, 204);
    }

    /**
     * Unlink MFP account for the user
     * @param Request $request
     * @param $id
     * @return void
     */
    public function unlinkMfp(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->removeMfp();
        return response(null, 204);
    }

    /**
     * Get users permissions
     * @return AdminUserPermissionsResource
     */
    public function permissions()
    {
        $role = auth('api')->user()->role;
        return new AdminUserPermissionsResource($role);
    }

    /**
     * Get all user roles
     * @return AnonymousResourceCollection
     */
    public function roles()
    {
        /** @var User $user */
        $user = auth()->user();
        $roles = UserRole::whereIn('slug', $user->userRolePermissionScopes())->get();
        return AdminUserPermissionsResource::collection($roles);
    }

    /**
     * Download progress picture by id
     *
     * @param Request $request
     * @param $id
     *
     * @return StreamedResponse
     */
    public function downloadProgressPicture(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $userFileService = app(UserFileService::class);
        $progressPicture = UserProgressPicture::findOrFail($id);
        $file = $userFileService->getPublicUrl($progressPicture);

        return response()->streamDownload(function () use ($file) {
            echo file_get_contents($file);
        }, $progressPicture->original_name, ['Content-Type' => 'image/jpeg'])->send();
    }

    public function resetUpdates(Request $request, $id)
    {
        ActivityLog::where('user_id', $id)
            ->where('action', 'UserUpdateDetails')
            ->where('created_at', '>', now()->subDays(7)->toDateTimeString())
            ->delete();
        return response()->json(['status' => 'success']);
    }

    public function resetConsent(Request $request, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        $user->setPreference('progress_picture_consent', 'unknown');
        $user->preferences->save();
        return response()->json(['status' => 'success']);
    }

    public function migrateWorkouts(Request $request)
    {
        $actions = new NotificationActions();
        $actions->workoutsV2toV3Migration();
        return response()->json(['status' => 'success']);
    }

    public function revertMigratedWorkouts(Request $request)
    {
        $user = Auth::user();
        $workoutPreferences = $user->workoutPreferences;
        $workoutPreferences->update([
            'schedule' => $workoutPreferences->data['workouts_v2_preferences']['schedule'],
            'exercise_frequency_id' => $workoutPreferences->data['workouts_v2_preferences']['exercise_frequency_id'],
            'exercise_level_id' => $workoutPreferences->data['workouts_v2_preferences']['exercise_level_id'],
            'exercise_location_id' => $workoutPreferences->data['workouts_v2_preferences']['exercise_location_id'],
            'data' => null
        ]);
        return response()->json(['status' => 'success']);
    }

    /**
     * Returns details for current logged in user
     * New endpoint for admin panel
     */
    public function whoAmI()
    {
        $user = User::with('role')
            ->where('id', Auth::id())
            ->first();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->surname,
                'email' => $user->email,
                'role' => $user->role->slug,
                'permissions' => $user->role->permissions
            ]
        ]);
    }

    /**
     * Reset a user account goals
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetUserGoals($id)
    {
        $user = User::findOrFail($id);
        try {
            $user->resetGoals();
            return response()->json([
                'message' => 'User reset'
            ]);
        } catch (\Exception $e) {
            throw new FitnessBadRequestException(
                'Sorry, there was an error. Please try again later.'
            );
        }
    }

    public function userAverages($id, Request $request)
    {
        $from = Carbon::parse($request->input('from'))->startOfWeek()->format('Y-m-d');
        $to = Carbon::parse($request->input('to'))->endOfWeek()->format('Y-m-d');
        $type = $request->input('type');

        $period = $request->input('period');
        $group = $period === 'weeks' ? 'W' : 'm';

        $activities = Activity::where('user_id', $id)
            ->where('type', $type)
            ->whereBetween('date', [$from, $to])
            ->get()
            ->filter(fn ($item) => $item->value != 0)
            ->groupBy(
                function ($date) use ($group) {
                    return Carbon::parse($date->date)->format($group);
                }
            )
            ->map(
                function ($item) use ($group) {
                    $avg = $item->avg('value');
                    $dates = $this->getDates($group, $item);
                    return [
                        'from' => $dates['start'],
                        'to' => $dates['end'],
                        'average' => number_format($avg, 1, '.', ''),
                    ];
                }
            )
            ->values()
            ->toArray();

        return response()->json([
            'data' => $activities
        ]);
    }

    public function userDataDownload(AdminUserDownloadRequest $request)
    {

        $user = User::find($request->input('user_id'));

        if ($request->input('start_date') == null) {
            $fromDate = Carbon::parse($user->created_at);
        } else {
            $fromDate = Carbon::parse($request->input('start_date'));
        }

        if ($request->input('end_date') == null) {
            $toDate = Carbon::now();
        } else {
            $toDate = Carbon::parse($request->input('end_date'));
        }

        $requestedActivities = $request->input('types');

        $activityService = new ActivityService();
        $activityService->setUser($user);
        $fileParams =  $activityService->generateUserActivityCsv($requestedActivities, $fromDate, $toDate);

        $headers = array(
            'Content-Type' => 'force-download',
            'Content-Disposition' => 'attachment'
        );

        return response()->download($fileParams['file_path'], $fileParams['file_name'], $headers)
            ->deleteFileAfterSend(true);
    }

    private function getDates(string $group, $item): array
    {
        if ($group === 'W') {
            $dates = $this->getDatesForWeek($item[0]->date);
        } else {
            $dates = $this->getDatesForMonth($item[0]->date);
        }
        return $dates;
    }

    private function getDatesForWeek($date)
    {
        $d = Carbon::parse($date);
        return [
            'start' => $d->startOfWeek()->format('Y-m-d'),
            'end' => $d->endOfWeek()->format('Y-m-d'),
        ];
    }

    private function getDatesForMonth($date)
    {
        $d = Carbon::parse($date);
        return [
            'start' => $d->startOfMonth()->format('Y-m-d'),
            'end' => $d->endOfMonth()->format('Y-m-d'),
        ];
    }
}
