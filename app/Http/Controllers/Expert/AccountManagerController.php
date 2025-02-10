<?php

namespace App\Http\Controllers\Expert;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\EmailUpdateRequest;
use App\Services\Expert\AccountService;
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
    public function getPaymentInfo()
    {
        return $this->accountService->getPaymentInfo();
    }
    public function createPaymentInfo(Request $request)
    {
        return $this->accountService->createPaymentInfo($request);
    }
    public function updateProfile(Request $request)
    {
        return $this->accountService->updateProfile($request);
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
