<?php

namespace App\Services\Admin;

use App\Models\Sla;
use App\Models\Category;
use App\Traits\ApiResponder;
use App\Models\Specialization;
use App\Models\SpecializationCategory;
use App\Http\Resources\CategoryResource;

class SpecializationCategoryService
{
    use ApiResponder;
    public function allCategories()
    {
        $categories = SpecializationCategory::latest('id')->get();
        $data = CategoryResource::collection($categories);
        return $this->successResponse($data);
    }

    public function storeCategory($data)
    {
        $specialization = SpecializationCategory::create([
            'name' => $data->name,
        ]);
        return $this->successResponse($specialization);
    }
    public function editCategory($id)
    {
        $category = SpecializationCategory::findOrFail($id);
        return $this->successResponse($category);
    }
    public function updateCategory($id, $data)
    {
        $category = SpecializationCategory::findOrFail($id);
        $check = SpecializationCategory::where('name', $data->name)->first();

        if ($check && $check->name != $category->name) {
            return $this->errorResponse("Name already exist", 422);
        }
        $category->update([
            'name' => $data->name,
        ]);
        return $this->successResponse("Details Updated");
    }
    public function deleteCategory($id)
    {
        $category = SpecializationCategory::find($id);
        if (!$category) {
            return $this->errorResponse("No record found", 422);
        }
        $category->delete();
        return $this->successResponse("Details Deleted");
    }

    public function allSpecializations()
    {
        $specializations =  Specialization::with('category')->latest('id')->get();
        return $this->successResponse($specializations);
    }

    public function storeSpecialization($data)
    {
        $specialization =  Specialization::create([
            'specialization_category_id' => $data->specialization_category_id,
            'name' => $data->name,
        ]);
        return $this->successResponse($specialization);
    }
    public function editSpecialization($id)
    {
        $specialization = Specialization::findOrFail($id);
        return $this->successResponse($specialization);
    }
    public  function updateSpecialization($id, $data)
    {
        $specialization = Specialization::findOrFail($id);
        $check = Specialization::where('name', $data->name)->first();

        if ($check && $check->name != $specialization->name) {
            return $this->errorResponse("Name already exist", 422);
        }

        $specialization->update([
            'specialization_category_id' => $data->specialization_category_id,
            'name' => $data->name,
        ]);
        return $this->successResponse("Details Updated");
    }
    public function deleteSpecialization($id)
    {
        $specialization = Specialization::find($id);
        if (!$specialization) {
            return $this->errorResponse("No record found", 422);
        }
        $specialization->delete();
        return $this->successResponse("Details deleted");
    }
}
