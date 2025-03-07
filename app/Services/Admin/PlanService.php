<?php

namespace App\Services\Admin;

use App\Models\IndividualPlan;
use App\Models\Plan;
use App\Traits\ApiResponder;

class PlanService
{
    use ApiResponder;
    public function getAll()
    {
        $plans =  Plan::latest('id')->get();
        if ($plans->isEmpty()) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($plans);
    }

    public  function store($data)
    {
        $plan = Plan::create([
            'title' => $data->title,
            'number_of_users' => $data->number_of_users,
            'type' => $data->type,
            'price' => $data->price,
            'duration' => $data->duration,
            'note' => $data->note,
            'features' => $data->features,
        ]);
        return $this->successResponse($plan);
    }

    public function storeIndividualPlan($data)
    {
        $plan = IndividualPlan::create([
            'name' => $data->name,
            'price' => $data->price,
            'note' => $data->note,
        ]);
        return $this->successResponse($plan);
    }
    public function allIndividualPlans()
    {
        $plans = IndividualPlan::latest('id')->get();
        return $this->successResponse($plans);
    }

    public function getIndividualPlan($id)
    {
        $plan = IndividualPlan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($plan);
    }

    public function updateIndividualPlan($id, $data)
    {
        $plan = IndividualPlan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        $plan->update([
            'name' => $data->name,
            'price' => $data->price,
            'note' => $data->note,
        ]);
        return $this->successResponse($plan);
    }
    public function deleteIndividualPlan($id)
    {
        $plan = IndividualPlan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        $plan->delete();
        return $this->successResponse('Plan deleted');
    }

    public function edit($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($plan);
    }

    public function update($id, $data)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 404);
        }
        $plan->update([
            'title' => $data->title,
            'number_of_users' => $data->number_of_users,
            'type' => $data->type,
            'price' => $data->price,
            'duration' => $data->duration,
            'note' => $data->note,
            'features' => $data->features,
        ]);
        return $this->successResponse($plan);
    }

    public function delete($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        $plan->delete();
        return $this->successResponse('Plan Deleted successfully');
    }
}
