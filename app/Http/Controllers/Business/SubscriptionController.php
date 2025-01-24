<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Services\Business\SubscriptionService;
use Illuminate\Http\Request;

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
    function storePlan(Request $request)  {
        return $this->subscriptionService->store($request);
    }
}
