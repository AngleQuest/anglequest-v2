<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\EmailUpdateRequest;
use App\Services\Auth\AccountService;

class AdminAuthController extends Controller
{
    public function __construct(
        private AdminService $adminService,
        private AccountService $accountService
    ) {}

    function profile()
    {
        return $this->adminService->getProfile();
    }

    public function changePassword(PasswordRequest $request)
    {
        return $this->adminService->updatePassword($request);
    }

    public function changeEmail(EmailUpdateRequest $request)
    {
        return $this->adminService->updateLoginDetails($request);
    }
    public function adminLogout(Request $request)
    {
        return $this->accountService->adminLogout($request);
    }
}
