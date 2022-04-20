<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessHttpException;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Rhf\Modules\Activity\Models\AchievementWeek;
use Rhf\Modules\Activity\Services\ActivityService;
use Rhf\Modules\Admin\Requests\UserRequest;
use Rhf\Modules\User\Models\User;
use Rhf\Modules\User\Models\UserProgress;
use Rhf\Modules\User\Services\UserFileService;
use Rhf\Modules\User\Services\UserService;
use Rhf\Modules\User\Resources\TabledUserResource;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\MyFitnessPal\Services\DiaryService;
use Rhf\Modules\User\Models\UserRole;

class UserController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user/index');
    }

    /**
     * Display the create user form.
     *
     * @return view
     */
    public function create(Request $request)
    {
        /** @var \Rhf\Modules\User\Models\User $user */
        $user = auth()->user();
        $exerciseLocations = ExerciseLocation::get();
        $exerciseFrequencies = ExerciseFrequency::get();
        $userRoles = UserRole::whereIn('slug', $user->userRolePermissionScopes())->get(['id', 'name', 'slug']);
        $availableRoles = $user->userRolePermissionScopes();

        return view('user/form', [
            'availableRoles' => $availableRoles,
            'userRoles' => $userRoles,
            'exerciseLocations' => $exerciseLocations,
            'exerciseFrequencies' => $exerciseFrequencies
        ]);
    }

    /**
     * Delete a user by ID.
     *
     * @param integer ID
     * @return view
     */
    public function delete(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->to('/admin/users')->with('message', [
            'status' => 'success',
            'message' => 'User successfully deleted.'
        ]);
    }

    /**
     * Display the edit user form.
     *
     * @return view
     */
    public function edit(Request $request, $id)
    {
        try {
            /** @var \Rhf\Modules\User\Models\User $authUser */
            $authUser = auth()->user();
            $user = User::find($id);
            $exerciseLocations = ExerciseLocation::get();
            $exerciseFrequencies = ExerciseFrequency::get();
            $userRoles = UserRole::whereIn('slug', $authUser->userRolePermissionScopes())->get(['id', 'name', 'slug']);
            $availableRoles = $authUser->userRolePermissionScopes();

            // Weeks for achievement retrieval
            $weeks = 3;
            $dates = [];
            for ($i = $weeks; $i > 0; $i--) {
                $dates[] = Carbon::now()->setTime(0, 0, 0)->subWeeks($i);
            }

            // Sync activity data across
            if ($user->hasConnectedMfp() && auth('api')->user()) {
                foreach ($dates as $date) {
                    DiaryService::sync(Carbon::parse($date), null, $user);
                }
            }

            // THIS IS NO LONGER USED!
            // Loop dates and create achievement week
            $achievementWeeks = [];
            foreach ($dates as $date) {
                $d = Carbon::parse($date);
                $activityService = new ActivityService();
                $activityService->setUser($user);
                $achievementWeek = new AchievementWeek(
                    $activityService->from($d)->to($d->endOfWeek())
                );

                $achievementWeeks[] = [
                    'date'  => $achievementWeek->getStartDate(),
                    'stars' => $achievementWeek->getWeeklyStarsByMetric(),
                    'medal' => $achievementWeek->getWeeklyMedal(),
                ];
            }
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return view('user/form', [
            'user' => $user,
            'exerciseLocations' => $exerciseLocations,
            'exerciseFrequencies' => $exerciseFrequencies,
            'achievementWeeks' => $achievementWeeks,
            'userRoles' => $userRoles,
            'availableRoles' => $availableRoles
        ]);
    }

    /**
     * Retrieve users by ajax.
     *
     * @return Json
     */
    public function get(Request $request)
    {
        // Filter and build the user collection
        $userCollection = UserService::filtered()->with('preferences')->get();

        // Calculate the current page
        if ($request->get('start') > 0 && $request->get('length') > 0) {
            $page = $request->get('start') / $request->get('length') + 1;
        } else {
            $page = 1;
        }

        // Size of page
        $size = $request->get('length');

        $users = new LengthAwarePaginator(
            $userCollection->slice(($page - 1) * $size, $size),
            $userCollection->count(),
            $size,
            $page
        );

        return response()->json([
            'data' => TabledUserResource::collection($users),
            'recordsTotal' => User::count(),
            'recordsFiltered' => $userCollection->count(),
        ]);
    }

    /**
     * Purge a user by ID.
     *
     * @param integer ID
     * @return redirect
     */
    public function purge(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // First delete prefs
            $user->preferences()->delete();

            $user->forceDelete();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->to('/admin/users')->with('message', [
            'status' => 'success',
            'message' => 'User and associated data has been successfully removed from the system.'
        ]);
    }

    /**
     * Restore a soft deleted user.
     *
     * @param Integer ID
     * @return redirect
     */
    public function restore(Request $request, $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            // Check user was trashed
            if (!$user->trashed()) {
                throw new FitnessHttpException('This user cannot be restored as they are already active.', 304);
            }

            $user->restore();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()->to('/admin/users/edit/' . $user->id)->with('message', [
            'status' => 'success',
            'message' => 'User successfully restored.'
            ]);
    }

    /**
     * Remove MyFitnessPal integration for user by ID.
     *
     * @param integer ID
     * @return redirect
     */
    public function unlinkMfp(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $success = $user->removeMfp();

        if ($success) {
            return redirect()
                ->to('/admin/users')
                ->with('message', [
                    'status' => 'success',
                    'message' => 'User has been successfully disconnected from MyFitnessPal.'
                ]);
        }

        return redirect()->back()->withErrors(['Unable to disconnect MyFitnessPal.']);
    }

    /**
     * Update or store a user.
     *
     * @return view
     */
    public function store(UserRequest $request, $id)
    {
        // Re-organise data into valid array for processing by service
        $data = [];
        foreach ($request->all() as $key => $value) {
            if ($key == 'meta') {
                foreach ($value as $metaKey => $metaValue) {
                    $data[$metaKey] = $metaValue;
                }
            } elseif ($key == '_token') {
                $data['token'] = $value;
            } elseif ($key == 'password') {
                // Dont set password
            } else {
                $data[$key] = $value;
            }
        }

        try {
            $user = User::findOrFail($id);

            $userService = new UserService();
            $userService->setUser($user)->update($data);

            // Check for password update
            if ($request->has('password') && $request->get('password') != '') {
                $user->updatePassword($request->get('password'));
            }
        } catch (Exception $e) {
            return redirect()
                ->to('/admin/users/edit/' . $id)
                ->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return redirect()->to('/admin/users/edit/' . $id)->with('message', [
            'status' => 'success',
            'message' => 'User successfully updated.'
        ]);
    }

    /**
     * Create a new user.
     *
     * @return view
     */
    public function storeNew(UserRequest $request)
    {
        // Create the empty user object
        try {
            $user = new User();
            $user->first_name = $request->get('first_name');
            $user->surname = $request->get('surname');
            $user->email = $request->get('email');
            $user->password = Hash::make(Str::random(8));

            // Handle that paid can be null from form
            $user->paid = $request->get('paid') == 1;

            $user->save();
            $user->preferences()->create();
            $user->workoutPreferences()->create();
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return $this->store($request, $user->id);
    }

    /**
     * Validate user details by ajax.
     *
     * @return Json
     */
    public function getValidate(Request $request)
    {
        try {
            $user = User::where('email', '=', $request->get('email'))->withTrashed()->first();

            if ($user && $user->trashed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This email address was previously used by a deleted account, would you like to ' .
                    'restore it? <a href="/admin/users/restore/' . $user->id . '" ' .
                    'class="btn btn-primary">Yes, Restore</a>'
                ], 200);
            }
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Could not validate user details - ' . $e->getMessage());
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Display a users progress pictures
     *
     * @return view
     */
    public function progress(Request $request, $id)
    {
        $user = User::with([
            'progress' => function ($q) {
                $q->with('progressPicture')->orderBy('id', 'desc');
            }
        ])->find($id);

        return view('user/progress', ['user' => $user, 'userProgress' => $user->progress]);
    }

    /**
     * Delete a users progress picture
     *
     * @return view
     */
    public function deleteProgressPicture(Request $request, $id)
    {
        // Grab the pivot first
        $progressPicturePivot = UserProgress::where('id', $id)->firstOrFail();

        // Grab the user from the pivot
        $user = $progressPicturePivot->user;

        $progressPictures = $progressPicturePivot->progressPicture->all();
        $userFileService = app(UserFileService::class);

        // Delete remotely off storage disk
        foreach ($progressPictures as $progressPicture) {
            $userFileService->delete($progressPicture);
        }

        try {
            // Delete the front and side images & pivot
            $progressPicturePivot->progressPicture()->delete();
            $progressPicturePivot->delete();
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        $user->fresh();
        return view('user/progress', ['user' => $user, 'userProgress' => $user->progress]);
    }
}
