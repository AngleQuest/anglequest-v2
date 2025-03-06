<?php

namespace App\Http\Controllers\Individual;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Services\ContentService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Individual\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private ContentService $contentService
    ) {}

    function index()
    {
        return $this->dashboardService->dashboardDetails();
    }
    public function cvAnalysis(Request $request)
    {
        return $this->contentService->cvAnalysis($request);
    }
    public function shortListStep(Request $request)
    {
        return $this->contentService->shortListStep($request);
    }
    public function getShortListStep()
    {
        return $this->contentService->getShortListStep();
    }
}
