<?php

namespace App\Http\Controllers\Individual;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubscriptionRequest;
use App\Services\Individual\AccountService;
use App\Services\Individual\DashboardService;

class AccountUpdateController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    function profile()
    {
        return $this->accountService->getProfile();
    }

    function updateProfile(Request $request)
    {
        return $this->accountService->updateProfile($request);
    }

    function getPlans()
    {
        return $this->accountService->getPlans();
    }

    function paymentHistory()
    {
        return $this->accountService->getPaymentHistory();
    }

    function subscribeToSla(Request $request)
    {
        return $this->accountService->subscribeToSla($request);
    }

    function createSubscription(SubscriptionRequest $request)
    {
        return $this->accountService->createSubscription($request);
    }

    function submitOtp(Request $request)
    {
        return $this->accountService->submitOtp($request);
    }
}
