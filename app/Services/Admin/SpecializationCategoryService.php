<?php

namespace App\Services\Admin;

use App\Models\Sla;
use App\Models\Category;
use App\Traits\ApiResponder;
use App\Models\Specialization;
use App\Models\SpecializationCategory;

class SpecializationCategoryService
{
    use ApiResponder;
    public function allCategories()
    {
        $categories = SpecializationCategory::with('specializations')->latest('id')->get();
        return $this->successResponse($categories);
    }

    public function storeCategory($data)
    {
        return SpecializationCategory::create([
            'name' => $data->name,
        ]);
    }
    public function editCategory($id)
    {
        $category = SpecializationCategory::find($id);
        return $category;
    }
    public function updateCategory($id, $data)
    {
        $category = SpecializationCategory::find($id);
        $category->update([
            'name' => $data->name,
        ]);
    }
    public function deleteCategory($id)
    {
        $category = SpecializationCategory::find($id);
        $category->delete();
    }

    public function allSpecializations()
    {
        return Specialization::with('category')->latest('id')->get();
    }

    public function storeSpecialization($data)
    {
        return Specialization::create([
            'specialization_category_id' => $data->specialization_category_id,
            'name' => $data->name,
        ]);
    }
    public function editSpecialization($id)
    {
        $specialization = Specialization::find($id);
        return $specialization;
    }
    public  function updateSpecialization($id, $data)
    {
        $specialization = Specialization::find($id);
        $specialization->update([
            'specialization_category_id' => $data->specialization_category_id,
            'name' => $data->name,
        ]);
        return $this->successResponse("Details Updated");
    }
    public function deleteSpecialization($id)
    {
        $specialization = Specialization::find($id);
        $specialization->delete();
    }
}
