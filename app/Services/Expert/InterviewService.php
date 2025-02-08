<?php

namespace App\Services\Expert;

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

use Agence104\LiveKit\VideoGrant;
use Agence104\LiveKit\AccessToken;
use App\Models\BusinessCardDetails;
use Illuminate\Support\Facades\Log;
use Agence104\LiveKit\AccessTokenOptions;

class InterviewService
{
    public function createGuide($data)
    {
        $supportRequest = Appointment::create([
            'user_id' => 2,
            'specialization' => $data->specialization,
            'title' => $data->title,
            'description' => $data->description,
            'expert_name' => $expert->first_name . ' ' . $expert->last_name,
            'individual_name' => 'sss',
            // 'appointment_date' => $data->appointment_date,
            'expert_id' => $data->expert_id,
            'status' => 'pending',
        ]);
        return response()->json([
            'status' => 'success',
            'data' => $supportRequest,
        ], 200);
    }

    public function allAppointments()
    {

        $user = Auth::user();
        $appointments = Appointment::where('expert_id', $user->id)
            ->where('status', 'active')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $appointments,
        ], 200);
    }


    public function acceptAppointment($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            return response()->json([
                'status' => 'failed',
                'data' => 'No record match',
            ], 422);
        }

        $expert = User::find(Auth::id());
        $user = User::where('id', $appointment->user_id)->first();

         $this->meetingLink($appointment, $user, $expert);

        $appointment->update([
            'status' => 'active'
        ]);

        return response()->json([
            'status' => 'success',
            'file' => $appointment,
            'data' => 'Request accepted successfully',
        ], 200);
    }
    private function meetingLink($request_details, $user, $expert)
    {

        // Logic to schedule a meeting with an expert and return the meeting details
        $roomName = "Support_Meeting _Scheduled_with_.$expert->last_name" . "_$user->first_name" . "_" . time();
        $candidateName = $user->email;
       $expertName = $expert->email;

        $candidateTokenOptions = (new AccessTokenOptions())->setIdentity($candidateName)->setTTL(86400);
        $expertTokenOptions = (new AccessTokenOptions())->setIdentity($expertName)->setTTL(86400);

        // Define the video grants.
        $candidateVideoGrant = (new VideoGrant())->setRoomJoin()->setRoomName($roomName)->setRoomJoin();
        $expertVideoGrant = (new VideoGrant())->setRoomJoin()->setRoomName($roomName)->setRoomAdmin(TRUE);

        $candidateToken = (new AccessToken(getenv('LIVEKIT_API_KEY'), getenv('LIVEKIT_API_SECRET')))->init($candidateTokenOptions)->setGrant($candidateVideoGrant)->toJwt();
        $expertToken = (new AccessToken(getenv('LIVEKIT_API_KEY'), getenv('LIVEKIT_API_SECRET')))->init($expertTokenOptions)->setGrant($expertVideoGrant)->toJwt();


        $request_details->update([
            'task_status' => 'active',
            'expert_link' => "https://meet.anglequest.work/$roomName/$expertToken",
            'individual_link' => "https://meet.anglequest.work/$roomName/$candidateToken",
        ]);

        $detail = [
            'subject' => "Support Meeting Scheduled with $expert->last_name $expert->first_name",
            'name' => $user->first_name,
            'schedule' => Carbon::now()->toDateString(),
            'link' => $request_details->meeting_link,
        ];

       // Mail::to([$user->email, Auth::user()->email])->send(new MeetingNotice($detail));
        return true;
    }
    public function appointmentFeedback($id)
    {

        $user = Auth::user();
        $feedback = AppointmentFeedback::where('user_id', $user->id)
            ->find($id);

        return response()->json([
            'status' => 'success',
            'data' => $feedback,
        ], 200);
    }
}
