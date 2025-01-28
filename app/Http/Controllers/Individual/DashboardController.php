<?php

namespace App\Http\Controllers\Individual;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Individual\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    function index()
    {
        return $this->dashboardService->dashboardDetails();
    }
}
