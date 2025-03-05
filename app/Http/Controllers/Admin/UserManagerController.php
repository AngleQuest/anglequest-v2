<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewUserRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Admin\UserManagerService;
use App\Http\Requests\PasswordUpdateRequest;

class UserManagerController extends Controller
{
    public function __construct(
        private UserManagerService $userService
    ) {}

    function users()
    {
        return $this->userService->getUsers();
    }

    function deActivateUser($id)
    {
        return $this->userService->deActivateUser($id);
    }
    function activateUser($id)
    {
        return $this->userService->activateUser($id);
    }
    function details($id)
    {
        return $this->userService->getSingleUser($id);
    }
    function updatePassword($id, PasswordUpdateRequest $request)
    {
        return $this->userService->updatePassword($id,$request);
    }
    function create(NewUserRequest $request)
    {
        return $this->userService->newUser($request);
    }

    function deleteUser($id)
    {
        return $this->userService->deleteUser($id);
    }
}
