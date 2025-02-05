<?php

namespace App\Services\Expert;

use App\Models\Hub;
use App\Models\Plan;
use App\Traits\ApiResponder;
use App\Http\Resources\HubResource;
use Illuminate\Support\Facades\Auth;

class HubService
{
    use ApiResponder;
    public function allHubs()
    {
        $hubs =  Hub::latest('id')->get();
        if ($hubs->isEmpty()) {
            return $this->errorResponse('No record found', 422);
        }
        $data =  HubResource::collection($hubs);
        return $this->successResponse($data);
    }

    public  function store($data)
    {

        $hub = Hub::create([
            'user_id' => Auth::id(),
            'visibility' => $data->visibility,
            'name' => $data->name,
            'category' => $data->category,
            'specialization' => $data->specialization,
            'description' => $data->description,
            'hub_goals' => $data->hub_goals,
        ]);
         $data = new HubResource($hub);
        return $this->successResponse($data);
    }
    public function edit($id)
    {
        $hub = Hub::find($id);
        if (!$hub) {
            return $this->errorResponse('No record found', 422);
        }
        $data = new HubResource($hub);
        return $this->successResponse($data);
    }

    public function update($id, $data)
    {
        $hub = Hub::find($id);
        if (!$hub) {
            return $this->errorResponse('No record found', 422);
        }
        $hub->update([
            'visibility' => $data->visibility,
            'name' => $data->name,
            'category' => $data->category,
            'specialization' => $data->specialization,
            'description' => $data->description,
            'hub_goals' => $data->hub_goals,
        ]);
        return $this->successResponse($hub);
    }

    public function delete($id)
    {
        $hub = Hub::find($id);
        if (!$hub) {
            return $this->errorResponse('No record found', 422);
        }
        $hub->delete();
        return $this->successResponse('Hub Deleted successfully');
    }
}
