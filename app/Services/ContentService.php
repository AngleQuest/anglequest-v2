<?php

namespace App\Services;

use App\Models\Sla;
use App\Models\Category;
use App\Models\CvAnalysis;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\ShortlistStep;
use App\Models\Specialization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SpecializationCategory;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Client\RequestException;
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
    public function categorySpecializations($id)
    {
        $specializations =  Specialization::where('specialization_category_id', $id)->get();
        return $this->successResponse($specializations);
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

    public function cvAnalysis($data)
    {

        if ($data->job_title) {
            try {
                if ($data->file('cv')) {
                    $file = $data->file('cv');

                    $bearer = 'Bearer MzWdRhAH26kmiA1oLGnRnOzND1i7teuBYGOAxTVNYkY';
                    $sendCv = Http::withHeaders([
                        'Authorization' => $bearer
                    ])->attach(
                        'files', //File to be uploaded
                        file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName()
                    )->post('https://ai.anglequest.work/api/v1/attachments/1d942442-715b-42f2-9cd3-edf217125638/anglequestcv', []);
                    // return json_decode($sendCv);
                    if ($sendCv->successful()) {
                        $result = json_decode($sendCv);


                        $predictionResponse = Http::timeout(60)
                            ->post('https://ai.anglequest.work/api/v1/prediction/1d942442-715b-42f2-9cd3-edf217125638', [
                                'question' => 'give me the analysis of the CV below, the person wants to become a ' . $data->job_title . ' : ' . $result[0]->content,
                                "chatId" => "anglequestcv" . Auth::id() . '.' . time(),
                            ]);

                        $cv_result = json_decode($predictionResponse);
                        CvAnalysis::updateOrCreate(
                            [
                                'user_id' => Auth::id(),
                            ],
                            [
                                'result' => $cv_result
                            ]
                        );
                        return $cv_result;
                    }
                } else {
                    return $this->errorResponse('No Cv uploaded, please upload one', 422);
                }
            } catch (\Throwable $th) {
                //return $th;
                return $this->errorResponse('Request failed or Invalid Cv content, please try again', 422);
            }
        } else {
            return $this->errorResponse('No Job title specified', 422);
        }
    }
    public function shortListStep($data)
    {

        try {
            if ($data) {
                $step = ShortlistStep::updateOrCreate(
                    [
                        'user_id' => Auth::id()
                    ],
                    [

                        'last_step' => $data->last_step
                    ]
                );
                if ($step) {
                    return $this->successResponse('Details captured');
                } else {
                    return $this->errorResponse('Details failed to be captured', 422);
                }
            }
        } catch (\Throwable $th) {

            return $th;
        }
    }
    public function getShortListStep()
    {
        $step = ShortlistStep::where('user_id', Auth::id())->first();
        if (!$step) {
            return $this->successResponse('No record found', 422);
        }
        return $this->successResponse($step);
    }
}
