<?php

namespace App\Services\Admin;

use App\Models\Sla;
use App\Traits\ApiResponder;

class SlaService
{
    use ApiResponder;
    public function getAll()
    {
        $data = Sla::latest('id')->get();
        return $this->successResponse($data);
    }

    public function store($data)
    {
        $data = Sla::create([
            'name' => $data->name,
            'features' => $data->features
        ]);
        return $this->successResponse($data);
    }

    public function edit($id)
    {
        $sla = Sla::find($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($sla);
    }

    public function updateSla($id, $data)
    {
        $sla = Sla::find($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 404);
        }
        $sla->update([
            'name' => $data->name,
            'features' => $data->features
        ]);
        return $this->successResponse("Details Updated");
    }

    public function delete($id)
    {
        $sla = Sla::find($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 404);
        }
        $sla->delete();
        return $this->successResponse("Details deleted");
    }
}
