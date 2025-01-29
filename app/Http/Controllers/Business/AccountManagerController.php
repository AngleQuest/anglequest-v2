<?php

namespace App\Http\Controllers\Business;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\EmailUpdateRequest;
use App\Services\Business\AccountService;
use App\Services\Business\EmployeeService;

class AccountManagerController extends Controller
{

    public function __construct(
        private AccountService $accountService
    ) {}
    public function profile()
    {
        return $this->accountService->getProfile();
    }

    public function changePassword(PasswordRequest $request)
    {
        return $this->accountService->updatePassword($request);
    }
    public function changeEmail(EmailUpdateRequest $request)
    {
        return $this->accountService->updateLoginDetails($request);
    }

    public function deleteMyAccount()
    {
        return $this->accountService->deleteAccount();
    }

   
}
