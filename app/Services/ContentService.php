<?php

namespace App\Services;

use App\Models\Sla;
use App\Models\Category;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\Specialization;
use App\Models\SpecializationCategory;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SpecializationResource;
use NunoMaduro\Collision\Adapters\Phpunit\ConfigureIO;

class ContentService
{
    use ApiResponder;
    public function allCategories()
    {
        $categories = SpecializationCategory::latest('id')->get();
        $data = CategoryResource::collection($categories);
        return $this->successResponse($data);
    }


    public function allSpecializations()
    {
        $specializations =  Specialization::latest('id')->get();
        $data = SpecializationResource::collection($specializations);
        return $this->successResponse($data);
    }
    public function configDetails()
    {
        $configuration =  Configuration::first();
        return $this->successResponse($configuration);
    }
}
