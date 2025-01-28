<?php

namespace App\Services\Individual;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use App\Models\SupportRequest;
use Illuminate\Support\Facades\Auth;

class SupportRequestService
{
    use ApiResponder;


    public function createSupport($data)
    {
        $expert = Expert::whereJsonContains('specialization', $data->specialization)->first();
        // return $expert->user_id;
        if ($expert) {
            $supportRequest = SupportRequest::where(['expert_id' => $expert->user_id, 'task_status' => 'active'])->count();
            if ($supportRequest <= 2) {
                if ($supportRequest <= 2) {
                    return response()->json([
                        'status' => 'success',
                        'expert' => 'expert with less load',
                        $expert,
                    ], 200);
                    //return $this->successResponse($expert);
                }
            } else {
                return response()->json([
                    'status' => 'success',
                    'expert' => 'expert with no load',
                    $expert,
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'data' => 'No expert found for this field',
            ], 404);
        }
    }

    public function mergeRequest($data)
    {

        $attachmentFilePath = $data->file('attachment')
            ? $data->file('attachment')->store('attachments', 'public')
            : null;
        //Found a match
        $expert = User::find($data->expert_id);
        $supportRequest = SupportRequest::create([
            'user_id' => Auth::user()->id,
            'specialization' => $data->specialization,
            'title' => $data->title,
            'description' => $data->description,
            'attachment' => $attachmentFilePath,
            'prefmode' => $data->prefmode,
            'priority' => $data->priority,
            'expert_name' => $expert->first_name . ' ' . $expert->last_name,
            'name' => $data->name,
            'deadline' => $data->deadline,
            'expert_id' => $data->expert_id,
        ]);
        return response()->json([
            'status' => 'success',
            'data' => $supportRequest,
        ], 200);
    }

    public function rateSupportRequest($id, $data)
    {
        $request_details = SupportRequest::find($id);

        if (!$request_details) {
            return $this->errorResponse(null, 'No record match');
        }

        $request_details->update([
            'rating' => $data->rating,
            'task_status' => 'completed'
        ]);
        return response()->json([
            'status' => 'success',
            'file' => $request_details,
            'data' => 'Request Rated and completed successfully',
        ], 200);
    }
    public function getActiveRequest()
    {
        $my_requests = SupportRequest::where(['user_id' => Auth::id(), 'task_status' => 'active']);

        if ($my_requests->isEmpty()) {
            return $this->errorResponse(null, 'No record match');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Active Requests',
            'data' => $my_requests,
        ], 200);
    }
    public function getCompletedRequest()
    {
        $my_requests = SupportRequest::where(['user_id' => Auth::id(), 'task_status' => 'completed']);

        if ($my_requests->isEmpty()) {
            return $this->errorResponse(null, 'No record match');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Active Requests',
            'data' => $my_requests,
        ], 200);
    }
}
