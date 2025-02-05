<?php

namespace App\Http\Controllers\Expert;

use Illuminate\Http\Request;
use App\Http\Requests\HubRequest;
use App\Services\Expert\HubService;
use App\Http\Controllers\Controller;

class HubManagerController extends Controller
{
    public function __construct(
        private HubService $hubService
    ) {}

    public function index()
    {
        return $this->hubService->allHubs();
    }
    public function store(HubRequest $request)
    {
        return $this->hubService->store($request);
    }


    public function show(string $id)
    {
        return $this->hubService->edit($id);
    }


    public function update($id, Request $request)
    {
        return $this->hubService->update($id, $request);
    }

    public function destroy(string $id)
    {
        return $this->hubService->delete($id);
    }
}
