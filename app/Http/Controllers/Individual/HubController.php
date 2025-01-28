<?php

namespace App\Http\Controllers\Individual;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Individual\IndividualHubService;

class HubController extends Controller
{
    public function __construct(
        private IndividualHubService $individualHubService
    ) {}

    function allHubs()
    {
        return $this->individualHubService->getAreaHub();
    }

    function joinHub($id)
    {
        return $this->individualHubService->attachHub($id);
    }
    function leaveHub($id)
    {
        return $this->individualHubService->leaveHub($id);
    }
}
