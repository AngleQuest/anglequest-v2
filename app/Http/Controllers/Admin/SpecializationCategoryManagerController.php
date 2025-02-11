<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\SpecializationCategoryService;

class SpecializationCategoryManagerController extends Controller
{
    use ApiResponder;
    public function __construct(
        private SpecializationCategoryService $categoryService
    ) {}

    public function allCategories()
    {
        return $this->categoryService->allCategories();
    }

    public function storeCategory(Request $request)
    {
        return $this->categoryService->storeCategory($request);
    }

    public function showCategory(string $id)
    {
        return $this->categoryService->editCategory($id);
    }
    public function updateCategory($id,Request $request)
    {
        return $this->categoryService->updateCategory($id, $request);
    }

    public function destroyCategory($id)
    {
        return $this->categoryService->deleteCategory($id);
    }
    public function allSpecializations()
    {
        return $this->categoryService->allSpecializations();
    }

    public function storeSpecialization(Request $request)
    {
        return $this->categoryService->storeSpecialization($request);
    }

    public function showSpecialization($id)
    {
        return $this->categoryService->editSpecialization($id);
    }
    public function updateSpecialization($id,Request $request)
    {
        return $this->categoryService->updateSpecialization($id, $request);
    }

    public function destroySpecialization($id)
    {
        return $this->categoryService->deleteSpecialization($id);
    }
}
