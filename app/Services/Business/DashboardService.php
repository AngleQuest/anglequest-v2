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

class DashboardService
{
    use ApiResponder;
    public function dashboardDetails()
    {
        $user = Auth::user();
        $employees = User::where('company_id', $user->company->id)->count();
        $data = [
            'company_name' => $user->company->name,
            'total_employees' => $employees,
        ];
        return $this->successResponse($data);
    }
}
