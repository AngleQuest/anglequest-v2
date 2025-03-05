<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewUserRequest;
use App\Services\Admin\ExpertManagerService;

class ExpertManagerController extends Controller
{
    public function __construct(
        private ExpertManagerService $expertService
    ) {}

    function getExperts()
    {
        return $this->expertService->getExperts();
    }


    function details($id)
    {
        return $this->expertService->getSingleExpert($id);
    }
    function updateProfile($id, Request $request)
    {
        return $this->expertService->updateExpert($id, $request);
    }
    function create(NewUserRequest $request)
    {
        return $this->expertService->newExpert($request);
    }

    function deleteAccount($id)
    {
        return $this->expertService->deleteExpert($id);
    }
}
