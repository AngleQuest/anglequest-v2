<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\PlanRequest;
use App\Services\Admin\PlanService;
use App\Http\Controllers\Controller;

class PlanManagerController extends Controller
{
    public function __construct(
        private PlanService $planService
    ) {}

    public function index()
    {
        return $this->planService->getAll();
    }
    public function store(PlanRequest $request)
    {
        return $this->planService->store($request);
    }
    public function storeIndividualPlan(Request $request)
    {
        return $this->planService->storeIndividualPlan($request);
    }

    public function allIndividualPlans()
    {
        return $this->planService->allIndividualPlans();
    }

    public function getIndividualPlan($id)
    {
        return $this->planService->getIndividualPlan($id);
    }

    public function updateIndividualPlan($id,Request $request)
    {
        return $this->planService->updateIndividualPlan($id,$request);
    }

    public function deleteIndividualPlan($id)
    {
        return $this->planService->deleteIndividualPlan($id);
    }

    public function show(string $id)
    {
        return $this->planService->edit($id);
    }

    public function update(Request $request, string $id)
    {
        return $this->planService->update($id, $request);
    }

    public function destroy(string $id)
    {
        return $this->planService->delete($id);
    }
}
