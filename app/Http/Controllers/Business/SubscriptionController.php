<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Business\SubscriptionService;
use App\Http\Requests\BussinessOnboardingRequest;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}
    function plans()  {
        return $this->subscriptionService->getPlans();
    }
    function paymentHistory()  {
        return $this->subscriptionService->getPaymentHistory();
    }
    function storePlan(BussinessOnboardingRequest $request)  {
        return $this->subscriptionService->store($request);
    }
}
