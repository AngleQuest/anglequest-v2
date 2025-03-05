<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Admin;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Payout;
use App\Models\Company;
use App\Enum\UserStatus;
use App\Models\AdminBank;
use App\Models\UserWallet;
use App\Enum\PaymentStatus;
use App\Models\ActivityLog;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Mail\OpenAccountEmail;
use App\Enum\AppointmentStatus;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\Individual;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\IndividualResource;

class UserManagerService
{
    use ApiResponder;

    public function getUsers()
    {
        $users =  User::latest('id')->get();
        if (!$users) {
            return $this->errorResponse('No record found', 404);
        }
        $data = UserResource::collection($users);
        return $this->successResponse($data);
    }
    public function deleteUser($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', 422);
        }
        IndividualProfile::where('user_id', $user->id)->delete();
        Expert::where('user_id', $user->id)->delete();
        $user->delete();
        ActivityLog::createRow('Admin','Admin Deleted ' . $user->email . ' Account');
        return $this->successResponse('Account deleted');
    }
    public function deActivateUser($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', 422);
        }
        $user->update([
            'status' => UserStatus::BLOCKED
        ]);
        ActivityLog::createRow(Auth::user()->username, ucfirst(Auth::user()->username) . ' De-activated ' . $user->username . ' Account');
        return $this->successResponse('Account de-activated');
    }
    public function activateUser($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', 422);
        }
        $user->update([
            'status' => UserStatus::ACTIVE
        ]);
        ActivityLog::createRow(Auth::user()->username, ucfirst(Auth::user()->username) . ' Activated ' . $user->username . ' Account');
        return $this->successResponse('Account activated');
    }


    public function getSingleUser($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return $this->errorResponse('No record found', 404);
        }
        $data = new UserResource($user);
        return $this->successResponse($data);
    }


    public function updateLoginDetails($data)
    {
        $admin = Admin::find(request()->user()->id);
        $admin->update([
            'email' => $data->email ?? $admin->email,
            'name' => $data->name ?? $admin->name,
        ]);
        return $this->successResponse($admin);
    }

    public function updatePassword($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('No user record found', 422);
        }

        $user->update([
            'password' => Hash::make($data->password),
        ]);
        return $this->successResponse('Password Successfully Updated');
    }
    
    public function newUser($data)
    {
        DB::beginTransaction();
        if ($data) {
            $password = Str::random(6);
            $user = User::create([
                'email' => strtolower($data->email),
                'mode' => 'open',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
                'role' => UserRole::INDIVIDUAL
            ]);
            IndividualProfile::create([
                'user_id' => $user->id,
                'name' => $data->name,
                'email' => $data->email
            ]);

            if ($user) {
                DB::commit();
                $detail = [
                    'email' => $data->email,
                    'password' => $password,
                ];
                ActivityLog::createRow($user->email, 'New Account created by Admin');
                Mail::to($user->email)->queue(new OpenAccountEmail($detail));
                return $this->successResponse($user);
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
}
