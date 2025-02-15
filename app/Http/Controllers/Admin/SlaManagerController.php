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

    public function index()
    {
        return $this->slaService->getAll();
    }

    public function store(Request $request)
    {
        return $this->slaService->store($request);
    }

    public function show($id)
    {
        return $this->slaService->edit($id);
    }

    public function update($id, Request $request)
    {

       return $this->slaService->updateSla($id, $request);

    }

    public function destroy($id)
    {
        return $this->slaService->delete($id);
    }
}
