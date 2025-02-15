<?php

namespace App\Http\Controllers\Individual;

use App\Http\Controllers\Controller;
use App\Http\Requests\SportRequest;
use App\Services\Individual\SupportRequestService;
use Illuminate\Http\Request;

class SupportRequestController extends Controller
{
    public function __construct(
        private SupportRequestService $supportRequestService
    ) {}

    function create(SportRequest $request)
    {
        return $this->supportRequestService->createSupport($request);
    }

    function mergeRequest(SportRequest $request)
    {
        return $this->supportRequestService->mergeRequest($request);
    }

    function activeRequest()
    {
        return $this->supportRequestService->getActiveRequest();
    }

    function completedRequest()
    {
        return $this->supportRequestService->getCompletedRequest();
    }
    function declinedRequest()
    {
        return $this->supportRequestService->getDeclinedRequest();
    }
}
