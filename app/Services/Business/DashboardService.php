<?php

namespace App\Services\Business;

use App\Enum\AppointmentStatus;
use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Mail\EmailVerification;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Resources\UserSubScriptionResource;
use App\Models\Appointment;

class DashboardService
{
    use ApiResponder;
    public function dashboardDetails()
    {
        $user = Auth::user();
        $employees = User::where('company_id',$user->company->id)->count();
       // $subscription = new UserSubScriptionResource($user->subscription);
        $data = [
            'company_name' => $user->company->name,
            'total_employees' => $employees,
          //  'subscription' => $subscription,
        ];
        return $this->successResponse($data);
    }
    public function hiringCandidates()
    {
        $candidates = Appointment::where('type', 'shortlisted')->where('status', AppointmentStatus::COMPLETED)->latest('id')->get();
        return $this->successResponse($candidates);
    }
    public function candidateDetails($id)
    {
      $candidate = Appointment::where('type','shortlisted')->where('status', AppointmentStatus::COMPLETED)->find($id);
      if (!$candidate) {
        return $this->errorResponse('No record found for this selection',422);
      }
        return $this->successResponse($candidate->individual);
    }
}
