<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Services\Admin\CompanyService;
use Illuminate\Http\Request;

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
    function updateCompany($id, CompanyRequest $request)
    {
        return $this->companyService->update($id, $request);
    }
    function deleteCompany($id)
    {
        return $this->companyService->delete($id);
    }
}
