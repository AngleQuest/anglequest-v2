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
use App\Enum\AppointmentStatus;
use App\Mail\EmailVerification;
use App\Services\UploadService;
use App\Models\AppointmentGuide;
use App\Models\IndividualProfile;
use App\Mail\InterviewPaymentMail;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\ValidationException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AppointmentService
{
    use ApiResponder;

    public function bookAppointment($data)
    {
        // $validated = $data->validate([
        //     'appointment_date' => 'required|date',
        //     'appointment_time' => 'required|date_format:H:i',
        // ]);
        $expert = Expert::whereJsonContains('specialization', $data->specialization)->first();
        if (!$expert) {
            return 'No expert found for your search';
        }

        // Check if the selected day and time are available
        // $dayOfWeek = date('l', strtotime($validated['appointment_date'])); // Get day of the week
        // $time = $validated['appointment_time'];

        // if (
        //     !isset($expert->available_days[$dayOfWeek]) ||
        //     !in_array($time, $expert->available_days[$dayOfWeek])
        // ) {
        //     throw ValidationException::withMessages([
        //         'appointment' => "The expert is not available on {$dayOfWeek} at {$time}."
        //     ]);
        // }

        // Check if the expert is already booked at this time
        // $isBooked = Appointment::where('expert_id', $expert->id)
        //     ->where('appointment_date', $validated['appointment_date'])
        //     ->where('appointment_time', $validated['appointment_time'])
        //     ->exists();

        // if ($isBooked) {
        //     throw ValidationException::withMessages([
        //         'appointment' => "The expert is already booked for this time slot."
        //     ]);
        // }

        //     Book the appointment
        //    $appointment = Appointment::create($validated);

        //     return response()->json([
        //         'message' => 'Appointment booked successfully.',
        //         'appointment' => $appointment
        //     ], 201);


        if ($expert) {
            $supportRequest = Appointment::where(['expert_id' => $expert->user_id, 'status' => 'active'])->count();
            if ($supportRequest <= 2) {
                return $this->successResponse($expert);
            } else {
                return 'No expert available for now';
            }
            return $this->successResponse($expert);
        }
    }

    public function pendingAppointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', AppointmentStatus::PENDING)
            ->get();
        return $this->successResponse($appointments);
    }
    public function allAppointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->latest('id')
            ->get();
        return $this->successResponse($appointments);
    }
    public function acceptedAppointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', AppointmentStatus::ACTIVE)
            ->get();
        return $this->successResponse($appointments);
    }

    public function declinedAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', AppointmentStatus::DECLINED)
            ->get();
        return $this->successResponse($appointments);
    }
    public function sendCV($data)
    {
        $appointment = Appointment::find($data->appointment_id);
        if (!$appointment) {
            return $this->errorResponse('No Appointment found with this Id', 404);
        }
        if ($data->file('cv')) {
            $uploadedImage = Cloudinary::upload($data->file('cv')->getRealPath(), [
                'folder' => 'cvs',
                'resource_type' => 'raw',
                'format' => 'pdf'
            ]);
            $cv = $uploadedImage->getSecurePath();
            $appointment->update([
                'cv' => $cv
            ]);
        }
        return $this->successResponse('Cv uploaded');
    }
    public function rateAppointment($data)
    {
        $appointment = Appointment::find($data->appointment_id);
        if (!$appointment) {
            return $this->errorResponse('No Appointment found with this Id', 404);
        }
        $appointment->update([
            'rating' => $data->rating
        ]);

        return $this->successResponse('Appointment rated');
    }

    public function completedAppointments()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)
            ->where('status', AppointmentStatus::COMPLETED)
            ->get();
        return $this->successResponse($appointments);
    }

    public function mergeAppointment($data)
    {
        $expert = User::where('role', UserRole::EXPERT)->find($data->expert_id);
        $expert_details = Expert::where('user_id', $data->expert_id)->first();
        $user = User::find(Auth::id());
        $profile = $user->profile;

        if (!$expert) {
            return $this->errorResponse('Expert not found', 422);
        }
        if ($data->file('cv')) {
            $uploadedImage = Cloudinary::upload($data->file('cv')->getRealPath(), [
                'folder' => 'cvs',
                'resource_type' => 'raw',
                'format' => 'pdf'
            ]);
            $cv = $uploadedImage->getSecurePath();
        }
        if ($data->file('job_description')) {
            $uploadedImage = Cloudinary::upload($data->file('job_description')->getRealPath(), [
                'folder' => 'jobdescriptions',
                'resource_type' => 'raw',
                'format' => 'pdf'
            ]);
            $job_description = $uploadedImage->getSecurePath();
        }
        DB::beginTransaction();
        try {
            $this->chargeCard($data, $user);
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'specialization' => $data->specialization,
                'title' => $data->title,
                'description' => $data->description,
                'job_description' => $data->job_description ? $job_description : null,
                'cv' => $data->cv ? $cv : null,
                'role' => $data->role,
                'title' => $data->title,
                'type' => $data->type ?? null,
                'category' => $data->category,
                'expert_photo' => $expert_details->profile_photo,
                'individual_photo' => $profile->profile_photo,
                'expert_name' => $expert_details->name,
                'individual_name' => $profile->name,
                'appointment_date' => $data->appointment_date,
                'appointment_time' => $data->appointment_time,
                'expert_id' => $data->expert_id,
                'status' => AppointmentStatus::PENDING,
            ]);
            DB::commit();
            $detail = [
                'name' => $profile->name ? $profile->fullName() : $data->name,
                'amount' => $data->amount,
                'expert' => $expert_details->name ? $expert_details->fullName() : $expert->name,
            ];
            Mail::to($user->email)->queue(new InterviewPaymentMail($detail));
            return response()->json([
                'status' => 'success',
                'data' => $appointment,
            ], 200);
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
        }
    }

    public function appointmentFeedback($id)
    {
        $user = Auth::user();
        $feedback = AppointmentFeedback::where('user_id', $user->id)->where('appointment_id', $id)->first();
        if (!$feedback) {
            return $this->errorResponse("No feedback available", 422);
        }
        return $this->successResponse($feedback);
    }

    public function chargeCard($data, $user)
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
                'customer_name' => $data->name ?? $user->username,
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
