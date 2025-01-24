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
