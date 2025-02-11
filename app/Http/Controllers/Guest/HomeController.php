<?php

namespace App\Http\Controllers\Guest;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Services\ContentService;
use App\Http\Controllers\Controller;
use App\Services\Admin\SpecializationCategoryService;

class HomeController extends Controller
{
    public function __construct(
        private ContentService $contentService
    ) {}
    public function allCategories()
    {
        return $this->contentService->allCategories();
    }
    public function allSpecializations()
    {
        return $this->contentService->allSpecializations();
    }
    public function configDetails()
    {
        return $this->contentService->configDetails();
    }
}
