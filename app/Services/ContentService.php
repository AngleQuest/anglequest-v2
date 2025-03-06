<?php

namespace App\Services;

use App\Models\Sla;
use App\Models\Category;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\Specialization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\SpecializationCategory;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Client\RequestException;
use App\Http\Resources\SpecializationResource;
use App\Models\CvAnalysis;
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
                    CvAnalysis::create([
                        'user_id' => Auth::id(),
                        'result' => $result
                    ]);
                    return $cv_result;
                }



                // if ($response->successful()) {
                //     $responseData = $response->json();
                //     if ($responseData['numAdded'] > 0) {
                //         $predictionResponse = Http::timeout(60) // Set the timeout to 60 seconds
                //             ->post('https://ai.anglequest.work/api/v1/prediction/2874fb18-9ef7-4463-8b7f-5da89b55e94e', [
                //                 'question' => 'give me the analysis of the CV, the person wants to become a ' . $data->job_title
                //             ]);
                //         $maxRetries = 5;
                //         $retryDelay = 1000; // Initial delay in milliseconds
                //         $success = false;
                //         $predictionResponse = null;

                //         for ($i = 0; $i < $maxRetries; $i++) {
                //             try {
                //                 $predictionResponse = Http::timeout(60) // Set the timeout to 60 seconds
                //                     ->post('https://ai.anglequest.work/api/v1/prediction/2874fb18-9ef7-4463-8b7f-5da89b55e94e', [
                //                         'question' => 'give me the analysis of the CV, the person wants to become a ' . $data->job_title
                //                     ]);

                //                 $success = true;
                //                 break; // Exit the loop if the request is successful
                //             } catch (RequestException $e) {
                //                 if ($i === $maxRetries - 1) {
                //                     // Log the error or handle the final failure
                //                     Log::error('Error getting questionnaire data: ' . $e->getMessage());
                //                 } else {
                //                     // Wait before retrying (exponential backoff)
                //                     usleep($retryDelay * 1000); // Convert milliseconds to microseconds
                //                     $retryDelay *= 2; // Exponential backoff
                //                 }
                //             }
                //         }

                //         if ($predictionResponse->successful()) {
                //             $analysisData = $predictionResponse->json();

                //             $cleanedJson = preg_replace('/json\n|\n/', '', $analysisData['text']);

                //             $data = json_decode($cleanedJson);

                //             Storage::delete('storage/' . $cv_path);

                //             // $analysis = CVAnalysis::where('user_id', $user->id)->first();

                //             // if($analysis) {
                //             //     $analysis->analysis = json_encode($data);
                //             //     $analysis->save();
                //             // } else {
                //             //     CVAnalysis::create([
                //             //         'user_id' => $user->id,
                //             //         'analysis' => json_encode($data)
                //             //     ]);
                //             // }

                //             return $data;
                //         } else {

                //             return  'Failed to generate prediction';
                //         }
                //     } else {
                //         Storage::delete('public/' . $cv_path);

                //         // Handle case where numAdded <= 0
                //         return response()->json([
                //             'status' => 'error',
                //             'message' => 'No data added'
                //         ], 400);
                //     }
                // } else {
                //     Storage::delete('storage/' . $cv_path);

                //     return 'Failed to analyze your cv';
                // }

            }
        } catch (\Throwable $th) {

            return $th;
        }
    }
}
