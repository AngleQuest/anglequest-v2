<?php

namespace App\Services\Expert;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Models\IncomeWallet;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Models\Configuration;
use App\Models\SupportRequest;
use App\Enum\AppointmentStatus;
use App\Mail\EmailVerification;
use App\Models\AppointmentGuide;
use Agence104\LiveKit\VideoGrant;
use App\Models\IndividualProfile;
use App\Models\TransactionWallet;

use Agence104\LiveKit\AccessToken;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentFeedback;
use App\Models\BusinessCardDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Agence104\LiveKit\AccessTokenOptions;

class InterviewService
{
    use ApiResponder;

    public function pendingAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->where('status', AppointmentStatus::PENDING)
            ->get();
        return $this->successResponse($appointments);
    }

    public function acceptedAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->where('status', AppointmentStatus::ACTIVE)
            ->get();
        return $this->successResponse($appointments);
    }

    public function completedAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->where('status', AppointmentStatus::COMPLETED)
            ->get();
        return $this->successResponse($appointments);
    }

    public function declinedAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->where('status', AppointmentStatus::DECLINED)
            ->get();
        return $this->successResponse($appointments);
    }
    public function allAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->latest('id')
            ->get();
        return $this->successResponse($appointments);
    }

    public function completeAppointment($id)
    {
        $config = Configuration::first();
        $expert = User::find(Auth::id());
        $appointment = Appointment::where('expert_id', $expert->id)->find($id);

        if (!$config) {
            return $this->errorResponse("Amount not set to credit expert", 422);
        }
        if (!$appointment) {
            return $this->errorResponse("No record found appointment", 422);
        }
        $user = User::find($appointment->user_id);
        if ($appointment->status == AppointmentStatus::COMPLETED) {
            return $this->errorResponse("Appointment already marked completed", 422);
        }
        DB::beginTransaction();
        $wallet = $expert->wallet;
        $wallet->master_wallet += $config->expert_fee;
        $wallet->save();
        $transaction = TransactionWallet::create([
            'user_id' => $expert->id,
            'payment_id' => 'AQ_' . Str::random(10) . time(),
            'type' => 'credit',
            'credit' => $config->expert_fee,
            'remark' => 'Appointment bonus from' . $user->profile->name,
            'status' => 'verified'
        ]);
        $income = IncomeWallet::create([
            'user_id' => $expert->id,
            'type' => 'Interview',
            'amount' => $config->expert_fee,
            'remark' => 'Appointment bonus from ' . $user->profile->name,
        ]);
        $appointment->update([
            'status' => AppointmentStatus::COMPLETED
        ]);
        if ($transaction && $income) {
            DB::commit();
            return $this->successResponse("Appointment marked completed");
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
    public function acceptAppointment($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->errorResponse("No record match", 422);
        }

        $expert = Expert::where('user_id', Auth::id())->first();
        $user = IndividualProfile::where('user_id', $appointment->user_id)->first();

        $this->meetingLink($appointment, $user, $expert);

        $appointment->update([
            'status' => AppointmentStatus::ACTIVE
        ]);
        return response()->json([
            'status' => 'success',
            'file' => $appointment,
            'data' => 'Request accepted successfully',
        ], 200);
    }

    public function rejectAppointment($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->errorResponse("No record match", 422);
        }
        $appointment->update([
            'status' => AppointmentStatus::DECLINED
        ]);

        return $this->successResponse("Request Declined successfully");
    }

    private function meetingLink($request_details, $user, $expert)
    {
        $key = "APIe6zT8wsZcTio";
        $seceret = "HBHaFZ9COxlv53bSGOFd8hpJGuvOK1b6DSRAOyVlZoA";
        // Logic to schedule a meeting with an expert and return the meeting details
        $roomName = "Support_Meeting _Scheduled_with_.$expert->name" . "_$user->name" . "_" . time();
        $candidateName = $user->name;
        $expertName = $expert->name;

        $candidateTokenOptions = (new AccessTokenOptions())->setIdentity($candidateName)->setTTL(86400);
        $expertTokenOptions = (new AccessTokenOptions())->setIdentity($expertName)->setTTL(86400);

        // Define the video grants.
        $candidateVideoGrant = (new VideoGrant())->setRoomJoin()->setRoomName($roomName)->setRoomJoin();
        $expertVideoGrant = (new VideoGrant())->setRoomJoin()->setRoomName($roomName)->setRoomAdmin(TRUE);

        $candidateToken = (new AccessToken($key, $seceret))->init($candidateTokenOptions)->setGrant($candidateVideoGrant)->toJwt();
        $expertToken = (new AccessToken($key, $seceret))->init($expertTokenOptions)->setGrant($expertVideoGrant)->toJwt();


        $request_details->update([
            'task_status' => 'active',
            'expert_link' => "https://meet.anglequest.work/$roomName/$expertToken",
            'individual_link' => "https://meet.anglequest.work/$roomName/$candidateToken",
        ]);

        $detail = [
            'subject' => "Support Meeting Scheduled with $expert->name $expert->name",
            'name' => $user->name,
            'schedule' => Carbon::now()->toDateString(),
            'link' => $request_details->meeting_link,
        ];

        // Mail::to([$user->email, Auth::user()->email])->send(new MeetingNotice($detail));
        return true;
    }

    public function appointmentFeedback($id, $data)
    {
        $user = Auth::user();
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return $this->errorResponse("No appointment found", 422);
        }
        AppointmentFeedback::create([
            'user_id' => $appointment->user_id,
            'expert_id' => $user->id,
            'appointment_id' => $appointment->id,
            'note' => $data->note,
            'rating' => $data->rating,
            'key_strengths' => $data->key_strengths,
            'improvements' => $data->improvements,
            'recommendation' => $data->recommendation,
        ]);

        return $this->successResponse("Feedback sent successfully");
    }

    public function viewAppointment($id)
    {
        $user = Auth::user();
        $appointment = Appointment::where('expert_id', $user->id)
            ->find($id);
        if (!$appointment) {
            return $this->errorResponse("No appointment found with this id", 422);
        }
        return $this->successResponse($appointment);
    }
}
