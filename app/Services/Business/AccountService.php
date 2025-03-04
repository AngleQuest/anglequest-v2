<?php

namespace App\Services\Business;

use Carbon\Carbon;
use App\Models\Hub;
use App\Models\Sla;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Models\UserHub;
use App\Models\UserSla;
use App\Enum\PaymentType;
use App\Mail\NewUserMail;
use App\Enum\AccountStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Models\PaymentHistory;
use App\Models\SupportRequest;
use App\Services\UploadService;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\Individual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AccountService
{
    use ApiResponder;

    public function getProfile()
    {
        return $this->successResponse(Auth::user()->company);
    }

    public function updateProfile($data)
    {
        $user = Auth::user();
        if (!empty($data->email) && User::where('email', $data->email)->where('id', '!=', $user->id)->exists()) {
            return $this->errorResponse("Email already exists.", 422);
        }

        if ($data->file('company_logo')) {
            $uploadedImage = Cloudinary::upload($data->file('company_logo')->getRealPath(), [
                'folder' => 'company'
            ]);
            $logo_url = $uploadedImage->getSecurePath();
        }
        if ($data->file('nda_file')) {
            $uploadedImage = Cloudinary::upload($data->file('nda_file')->getRealPath(), [
                'folder' => 'companyNDA',
                'resource_type' => 'raw',
                'format' => 'pdf'
            ]);
            $nda_url = $uploadedImage->getSecurePath();
        }


        $user->company->update([
            'name' => $data->name ?? $user->company->name,
            'administrator_name' => $data->administrator_name ?? $user->company->administrator_name,
            'email' => $data->email ?? $user->company->email,
            'address' => $data->address ?? $user->company->address,
            'nda_file' => $data->nda_file ? $nda_url : $user->company->nda_file,
            'company_logo' => $data->company_logo ? $logo_url : $user->company->company_logo,
            'business_reg_number' => $data->business_reg_number ?? $user->company->business_reg_number,
            'business_phone' => $data->business_phone ?? $user->company->business_phone,
            'company_size' => $data->company_size ?? $user->company->company_size,
            'website' => $data->website ?? $user->company->website,
            'about' => $data->about ?? $user->company->about,
            'service_type' => $data->service_type ?? $user->company->service_type,
            'country' => $data->country ?? $user->company->country,
            'city' => $data->city ?? $user->company->city,
            'state' => $data->state ?? $user->company->state,
        ]);

        $user->update([
            'email' => $data->email,
        ]);

        ActivityLog::createRow($user->email, ucfirst($user->email) . 'Updated profile ' . $user->role . ' Account');
        return $this->successResponse(Auth::user()->company);
    }

    public function updateLoginDetails($data)
    {
        $user = User::find(Auth::id());
        $user->update([
            'email' => $data->email ?? $user->email,
        ]);
        return $this->successResponse($user);
    }

    public function deleteAccount()
    {
        $user = User::find(Auth::id());
        $employees = User::where('company_id', $user->company_id)->get();
        foreach ($employees as $employee) {
            $employee->delete();
            $employee->tokens()->delete();
        }
        $user->tokens()->delete();
        return $this->successResponse('Account Deleted');
    }

    public function blockAccount()
    {
        $user = User::find(Auth::id());
        $employees = User::where('company_id', $user->company_id)->get();
        foreach ($employees as $employee) {
            $employee->status = AccountStatus::BLOCKED;
            $employee->save();
        }
        $user->update([
            'status' => AccountStatus::BLOCKED,
        ]);
        return $this->successResponse('Account Blocked');
    }

    public function updatePassword($data)
    {
        $user = User::find(Auth::id());
        if (!Hash::check($data->old_password, $user->password)) {
            return $this->errorResponse('Old Password does not match', 422);
        }

        $user->update([
            'password' => Hash::make($data->password),
        ]);
        return $this->successResponse('Password Successfully Updated');
    }

    public function subscribeToSla($data)
    {
        $sla = Sla::find($data->sla_id);
        if (!$sla) {
            return $this->errorResponse('No record found', 422);
        }
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $subscription = UserSla::create([
                'user_id' => $user->id,
                'sla_id' => $sla->id,
            ]);
            if ($subscription) {
                DB::commit();

                return $this->successResponse($subscription);
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
        }
    }
}
