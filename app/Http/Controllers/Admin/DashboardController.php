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

    function deActivateUser($id)
    {
        return $this->adminService->deActivateUser($id);
    }
    function activateUser($id)
    {
        return $this->adminService->activateUser($id);
    }

    function deleteUser($id)
    {
        return $this->adminService->deleteUser($id);
    }

    function experts()
    {
        return $this->adminService->getExperts();
    }
    function getSingleExpert($id)
    {
        return $this->adminService->getSingleExpert($id);
    }
    function individuals()
    {
        return $this->adminService->getIndividuals();
    }
    function getSingleIndividual($id)
    {
        return $this->adminService->getSingleIndividual($id);
    }
    function companies()
    {
        return $this->adminService->getCompanies();
    }
    function getSingleCompany($id)
    {
        return $this->adminService->getSingleCompany($id);
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
