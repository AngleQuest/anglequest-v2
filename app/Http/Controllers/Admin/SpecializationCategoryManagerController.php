<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SpecializationCategoryService;

class SpecializationCategoryManagerController extends Controller
{
    use ApiResponder;
    public function __construct(
        private SpecializationCategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->allCategories();
        if (count($categories) < 1) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($categories);
    }



    /**
     * Store a newly created Sla in storage.
     */
    public function store(Request $request)
    {
        if (!$request->name) {
            return $this->errorResponse('Please input Category title before proceeding', 422);
        }
        return $this->successResponse($this->categoryService->store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = $this->categoryService->edit($id);
        if (!$category) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($category);
    }


    /**
     * Update the specified SLA.
     */
    public function update(Request $request, string $id)
    {
        if (!$request->name) {
            return $this->errorResponse('Please input Category title before proceeding', 422);
        }
        $category = $this->categoryService->edit($id);
        if (!$category) {
            return $this->errorResponse('No record found', 422);
        }
        $this->categoryService->update($id, $request);
        return $this->successResponse('Detials updated');
    }

    /**
     * Remove the specified Sla from storage.
     */
    public function destroy(string $id)
    {
        $category = $this->categoryService->edit($id);
        if (!$category) {
            return $this->errorResponse('No record found', 422);
        }
        $this->categoryService->delete($id);
        return $this->successResponse('Record Deleted successfully');
    }
}
