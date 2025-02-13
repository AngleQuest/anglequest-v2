<?php

namespace App\Services\Individual;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Models\PaymentHistory;
use App\Models\SupportRequest;
use App\Mail\EmailVerification;
use App\Mail\InterviewPaymentMail;
use App\Models\AppointmentGuide;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
        DB::beginTransaction();
        try {
            $this->chargeCard($data, $user);
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
                'expert_name' => $expert_details->first_name ? $expert_details->fullName() : $expert->username,
                'individual_name' => $profile->first_name ? $profile->fullName() : $user->username,
                'appointment_date' => $data->appointment_date,
                'expert_id' => $data->expert_id,
                'status' => 'pending',
            ]);
            DB::commit();
            $detail = [
                'name' => $profile->first_name ? $profile->fullName() : $user->username,
                'amount' => $data->amount,
                'expert' => $expert_details->first_name ? $expert_details->fullName() : $expert->username,
            ];
            Mail::to($user->email)->queue(new InterviewPaymentMail($detail));
            return response()->json([
                'status' => 'success',
                //'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
        }
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

    private function chargeCard($data, $user)
    {

        $url = 'https://api.paystack.co/charge';
        $secretKey = 'sk_test_3500fbfb097c5237d9e30fec3c6d56e8dbc3a106';
        $response = Http::withToken($secretKey)->post($url, [
            'email' => $user->email,
            'amount' => $data->amount * 100,
            'card' => [
                'number' => $data->card_number,
                'cvv' => $data->cvv,
                'expiry_month' => $data->expiry_month,
                'expiry_year' => $data->expiry_year,
            ],
            'metadata' => [
                'customer_name' => $user->profile->first_name ? $user->profile->fullName() : $user->username,
                'customer_email' => $user->email,
            ]
        ]);

        $responseBody = $response->json();

        if ($response->successful() && $responseBody['status']) {
            // Check if further authentication is required
            // if ($responseBody['data']['status'] === 'send_otp') {
            //     return response()->json([
            //         'message' => 'OTP required',
            //         'data' => $responseBody['data'],
            //     ]);
            // }
            $payment_id = 'AQ_' . Str::random(25) . time();
            PaymentHistory::create([
                'user_id' => Auth::id(),
                'type' => 'subscription',
                'amount' => $data->amount,
                'payment_type' => 'interview sesstion',
                'payment_id' => $payment_id,
                'method' => PaymentMethod::PAYSTACK,
                'status' => PaymentStatus::PAID,
            ]);
            return true;
        }

        return response()->json([
            'message' => 'Payment failed',
            'error' => $responseBody['message'],
        ], 400);
    }
}
