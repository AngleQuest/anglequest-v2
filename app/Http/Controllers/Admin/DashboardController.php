<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;

class DashboardController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    function index()
    {
        return $this->adminService->getDashboardData();
    }

    function users()
    {
        return $this->adminService->getUsers();
    }

    function experts()
    {
        return $this->adminService->getExperts();
    }
    function companies()
    {
        return $this->adminService->getCompanies();
    }
    function withdrawalRequests()
    {
        return $this->adminService->withdrawalRequests();
    }
    function approveRequest($id)
    {
        return $this->adminService->approveRequest($id);
    }
    function declineRequest($id)
    {
        return $this->adminService->declineRequest($id);
    }
}
