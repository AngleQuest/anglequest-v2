<?php

namespace App\Services;

use App\Models\Sla;
use App\Models\Category;
use App\Models\SpecializationCategory;

class SpecializationCategoryService
{
    public static function allCategories()
    {
        return SpecializationCategory::latest('id')->get();
    }

    public static function store($data)
    {
        return SpecializationCategory::create([
            'name' => $data->name,
        ]);
    }
    public static function edit($id)
    {
        $category = SpecializationCategory::find($id);
        return $category;
    }
    public static function update($id, $data)
    {
        $category = SpecializationCategory::find($id);
        $category->update([
            'name' => $data->name,
        ]);
    }
    public static function delete($id)
    {
        $category = SpecializationCategory::find($id);
        $category->delete();
    }
}
