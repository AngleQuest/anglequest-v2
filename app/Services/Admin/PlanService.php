<?php

namespace App\Services\Admin;

use App\Models\Plan;
use App\Traits\ApiResponder;

class PlanService
{
    use ApiResponder;
    public function getAll()
    {
        $plans =  Plan::latest('id')->get();
        if ($plans->isEmpty()) {
            return $this->errorResponse('No record found', 422);
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
            'fetures' => $data->features,
        ]);
        return $this->successResponse($plan,200);
    }
    public function edit($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($plan, 200);
    }

    public function update($id, $data)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        $plan->update([
            'title' => $data->title,
            'number_of_users' => $data->number_of_users,
            'type' => $data->type,
            'price' => $data->price,
            'duration' => $data->duration,
            'note' => $data->note,
            'fetures' => $data->features,
        ]);
        return $this->successResponse($plan, 200);
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
