<?php

namespace App\Services\Individual;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Models\SupportRequest;
use App\Mail\EmailVerification;
use App\Models\Appointment;
use App\Models\AppointmentFeedback;
use App\Models\AppointmentGuide;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AppointmentService
{
    use ApiResponder;

    public function bookAppointment($data)
    {

        $expert = AppointmentGuide::whereJsonContains('specialization', $data->specialization)->first();
        // return $expert->user_id;
        if ($expert) {
            $supportRequest = Appointment::where(['expert_id' => $expert->user_id, 'status' => 'active'])->count();
            if ($supportRequest >= 2) {
                if ($supportRequest >= 2) {
                    return response()->json([
                        'status' => 'success',
                        'expert' => 'expert with less load',
                        $expert,
                    ], 200);
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

    public function declinedAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', 'declined')
            ->get();
        return $this->successResponse($appointments);
    }

    public function completedAppointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();
        return $this->successResponse($appointments);
    }

    public function mergeAppointment($data)
    {
        $expert = User::find($data->expert_id);
        if (!$expert) {
            return $this->errorResponse('Expert not found', 422);
        }
        $expert_details = Expert::where('user_id', $data->expert_id)->first();
        $user = User::find(Auth::id());
        $profile = $user->profile;
        $data = [
            'user' => $user,
            'profile' => $profile,
        ];
        return $data;
        $appointment = Appointment::create([
            'user_id' => $user->id,
            'specialization' => $data->specialization,
            'title' => $data->title,
            'description' => $data->description,
            'job_description' => $data->job_description,
            'cv' => $data->cv,
            'role' => $data->role,
            'title' => $data->title,
            'category' => $data->category,
            'expert_name' => $expert_details->fullName() ?? $expert->username,
            'individual_name' => $expert_details->fullName() ?? $expert->username,
            'appointment_date' => $data->appointment_date,
            'expert_id' => $data->expert_id,
            'status' => 'pending',
        ]);
        return response()->json([
            'status' => 'success',
            'data' => $appointment,
        ], 200);
    }

    public function appointmentFeedback($id)
    {
        $user = Auth::user();
        $feedback = AppointmentFeedback::where('user_id', $user->id)
            ->find($id);
        if (!$feedback) {
            return $this->errorResponse("No feedback available", 422);
        }
        return $this->successResponse($feedback);
    }
}
