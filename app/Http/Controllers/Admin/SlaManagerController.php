<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\SlaService;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SlaManagerController extends Controller
{
    use ApiResponder;
    public function __construct(
        private SlaService $slaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slas = $this->slaService->getAll();
        foreach ($slas as $value) {
            $value->features = json_decode($value->features);
        }
        return $this->successResponse($slas);
    }



    /**
     * Store a newly created Sla in storage.
     */
    public function store(Request $request)
    {
        if (!$request->name) {
            return $this->errorResponse('Please input SLA title before proceeding', 422);
        }
        return $this->successResponse($this->slaService->store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sla = $this->slaService->edit($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 422);
        }
        $sla->features = json_decode($sla->features);
        return $this->successResponse($sla);
    }


    /**
     * Update the specified SLA.
     */
    public function update(Request $request, string $id)
    {
        if (!$request->name) {
            return $this->errorResponse('Please input SLA title before proceeding', 422);
        }
        $sla = $this->slaService->edit($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 422);
        }
        $this->slaService->update($id, $request);
        return $this->successResponse('Detials updated');
    }

    /**
     * Remove the specified Sla from storage.
     */
    public function destroy(string $id)
    {
        $sla = $this->slaService->edit($id);
        if (!$sla) {
            return $this->errorResponse('No record found', 422);
        }
        $this->slaService->delete($id);
        return $this->successResponse('Record Deleted successfully');
    }
}
