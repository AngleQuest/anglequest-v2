<?php

namespace App\Services\Business;

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

class DashboardService
{
    use ApiResponder;
    public function dashboardDetails()
    {
        $user = Auth::user();
        $employees = User::where('company_id', $user->company->id)->count();
        $subscription = new UserSubScriptionResource($user->subscription);
        $data = [
            'company_name' => $user->company->name,
            'total_employees' => $employees,
            'subscription' => $subscription,
        ];
        return $this->successResponse($data);
    }
}
