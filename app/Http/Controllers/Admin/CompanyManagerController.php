<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\Admin\CompanyService;
use App\Http\Requests\CompanyProfileUpdateRequest;

class CompanyManagerController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    function index()
    {
        return $this->companyService->getAll();
    }
    function create(CompanyRequest $request)
    {
        return $this->companyService->store($request);
    }
    function edit($id)
    {
        return $this->companyService->edit($id);
    }
    function updateCompany($id, CompanyProfileUpdateRequest $request)
    {
        return $this->companyService->update($id, $request);
    }
    function deleteCompany($id)
    {
        return $this->companyService->delete($id);
    }
}
