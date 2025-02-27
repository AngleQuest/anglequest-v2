<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Business\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct(
        private DashboardService $dashboardService
    ) {}

    function index()
    {
        return $this->dashboardService->dashboardDetails();
    }
    function hiringCandidates()
    {
        return $this->dashboardService->hiringCandidates();
    }
    function candidateDetails($id)
    {
        return $this->dashboardService->candidateDetails($id);
    }
}
