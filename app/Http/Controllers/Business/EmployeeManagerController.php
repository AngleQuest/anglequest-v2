<?php

namespace App\Http\Controllers\Business;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Services\Business\EmployeeService;

class EmployeeManagerController extends Controller
{
    
    public function __construct(
        private EmployeeService $employeeService
    ) {}
    public function index()
    {
        return $this->employeeService->getAllEmployees();
    }

    public function addEmployee(EmployeeRequest $request)
    {
        return $this->employeeService->store($request);
    }
    public function inviteEmployeeViaEmail(Request $request)
    {
        return $this->employeeService->emailInvitaion($request);
    }

    public function deleteEmployee($id)
    {
        return $this->employeeService->delete($id);
    }
    public function uploadCSV(Request $request)
    {
        return $this->employeeService->UploadEmployees($request);
    }

    public function deleteEmployees(Request $request)
    {
        return $this->employeeService->bulkDelete($request);
    }
}
