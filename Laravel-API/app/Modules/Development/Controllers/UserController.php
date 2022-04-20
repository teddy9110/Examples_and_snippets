<?php

namespace Rhf\Modules\Development\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Development\Requests\UserCreationRequest;
use Rhf\Modules\Development\Requests\UserDeletionRequest;
use Rhf\Modules\Development\Resources\UserResource;
use Rhf\Modules\Development\Services\UserService;

class UserController extends Controller
{
    public function createUser(UserCreationRequest $request)
    {
        $userService = new UserService();
        $user = $userService->createUser(
            $request->json('password'),
            $request->json('role'),
            $request->json('paid')
        );

        if ($request->json('status') == 'onboarded') {
            $user->active = true;
            $user->save();
            $userService->seedUserPreferences($user);
        }
        return new UserResource($user);
    }

    public function removeUsers(UserDeletionRequest $request)
    {
        $userService = new UserService();
        $userService->deleteUser($request->json('user_id'));
        return response()->noContent();
    }
}
