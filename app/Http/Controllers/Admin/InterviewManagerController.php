<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;

class InterviewManagerController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    function pending()
    {
        return $this->adminService->pendingAppointments();
    }
    function active()
    {
        return $this->adminService->acceptedAppointments();
    }
    function completed()
    {
        return $this->adminService->completedAppointments();
    }
    function declined()
    {
        return $this->adminService->declinedAppointments();
    }
}
