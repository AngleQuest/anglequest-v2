<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    function adminAccountDetails()
    {
        return $this->adminService->getAccountDetails();
    }
    function updateAccountDetails(Request $request)
    {
        return $this->adminService->updateDetails($request);
    }
}
