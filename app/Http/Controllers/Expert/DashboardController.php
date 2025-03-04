<?php

namespace App\Http\Controllers\Expert;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Expert\DashboardService;

class DashboardController extends Controller
{
    use ApiResponder;
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    function index()
    {
        return $this->dashboardService->dashboardDetails();
    }
}
