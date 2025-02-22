<?php

namespace App\Services\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\UserWallet;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\ProductRating;
use App\Models\ProductReview;
use App\Mail\OpenAccountEmail;
use App\Mail\EmailVerification;
use App\Models\ActivityLog;
use App\Models\AppointmentGuide;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountService
{
    use ApiResponder;

    public  function openAccount($data)
    {
        DB::beginTransaction();
        if ($data) {
            $password = Str::random(5);
            $user = User::create([
                'email' => strtolower($data->email),
                'username' => str_replace(' ', '', $data->username),
                'mode' => 'open',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
                'role' => UserRole::INDIVIDUAL
            ]);
            $user->token = $user->createToken($user->username . 'API Token')->plainTextToken;
            IndividualProfile::create([
                'user_id' => $user->id
            ]);

            if ($user) {
                DB::commit();
                $detail = [
                    'email' => $data->email,
                    'name' => $data->username,
                    'password' => $password,
                ];
                ActivityLog::createRow($user->id, $user->username,'New Appointment booked by '.ucfirst($user->username));
                Mail::to($user->email)->queue(new OpenAccountEmail($detail));
                return $this->successResponse($user);
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
    public  function checkExpert($data)
    {
        $expert = AppointmentGuide::whereJsonContains('specialization', $data->specialization)->first();
        if (!$expert) {
            return 'No expert found for your search';
        }
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

    public  function signUp($data)
    {
        $user = '';
        DB::beginTransaction();
        if ($data) {
            $code = mt_rand(100000, 999999);
            if ($data->role == UserRole::INDIVIDUAL) {
                if (!$data->username) {
                    return $this->errorResponse('Please enter your Username.', 422);
                }
                $user = User::create([
                    'email' => strtolower($data->email),
                    'username' => str_replace(' ', '', $data->username),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
                IndividualProfile::create([
                    'user_id' => $user->id
                ]);
            }
            if ($data->role == UserRole::EXPERT) {
                if (!$data->username) {
                    return $this->errorResponse('Please enter your Username.', 422);
                }
                $user = User::create([
                    'email' => strtolower($data->email),
                    'username' => str_replace(' ', '', $data->username),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
                UserWallet::create([
                    'user_id' => $user->id
                ]);
                Expert::create([
                    'user_id' => $user->id
                ]);
            }
            if ($data->role == UserRole::BUSINESS) {
                if (!$data->company_name) {
                    return $this->errorResponse('Please enter company name.', 422);
                }
                if (!$data->administrator_name) {
                    return $this->errorResponse('Please enter administrator name.', 422);
                }
                $company = Company::create([
                    'name' => $data->company_name,
                    'administrator_name' => $data->administrator_name,
                    'email' => $data->email,
                ]);
                $user = User::create([
                    'company_id' => $company->id,
                    'username' => str_replace(' ', '',$data->administrator_name),
                    'email' => strtolower($data->email),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
            }

            if ($user) {
                DB::commit();
                $user->update([
                    'email_code' => $code,
                    'email_code_expire_time' => Carbon::now()->addMinutes(30),
                ]);
                Mail::to($user->email)->queue(new EmailVerification($user));
                $user->token = $user->createToken($user->username . 'API Token')->plainTextToken;
                ActivityLog::createRow($user->username,ucfirst($user->username).' Signed up using '.$data->role.' Account');
                return $this->successResponse($user);
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }

    public function login($data)
    {

        $user = User::where('email', strtolower($data->email))->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }
        if (!$user->email_verified_at) {
            return $this->errorResponse('Please verify your email.', 422);
        }

        $credentials = ['email' => $data->email, 'password' => $data->password];

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Invalid Credentials inputted, please try it again.', 422);
        }

        if (strtolower($user->status) == 'blocked' || strtolower($user->status) == 'suspended') {
            return $this->errorResponse('This account has been Blocked / Suspended. Please Contact support for activation.', 422);
        }

        //event(new Login($user));
        $user->token = $user->createToken($user->email . ' Login Token')->plainTextToken;
        ActivityLog::createRow($user->username,ucfirst($user->username).' Logged in using '.$data->role.' Account');
        return $this->successResponse($user);
    }

    public function emailVerification($data)
    {

        if (!$data->email_code) {
            return $this->errorResponse('The Email Code field is required', 422);
        }
        $user = User::where('email_code', $data->email_code)->first();
        if (!$user) {
            return $this->errorResponse('Invalid code inputted, please try it again.', 422);
        }
        if ($user->email_code_expire_time < now()->toDateTimeString()) {
            return $this->errorResponse('Verification Code has Expired!', 422);
        }
        $user->update([
            'email_verified_at' => Carbon::now(),
            'email_code' => null,
            'email_code_expire_time' => null,
        ]);
        Mail::to($user->email)->send(new NewUserMail($user));
        ActivityLog::createRow($user->username,ucfirst($user->username).' did email verification using '.$user->role.' Account');
        return $this->successResponse('Email Verified Succesfully');
    }

    public function resendCode($data)
    {

        $user = User::where('email', Auth::user() ? Auth::user()->email : $data->email)->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }

        $user->update([
            'email_code' => rand(100000, 999999),
            'email_code_expire_time' => Carbon::now()->addMinutes(30)
        ]);

        $user = User::find($user->id);

        Mail::to($user)->send(new EmailVerification($user));
        ActivityLog::createRow($user->username,ucfirst($user->username).' Requested for a new verication code using '.$user->role.' Account');
        return $this->successResponse('A new code has been sent to you');
    }

    public function verifyUserIdentity($data)
    {

        if (!$data->email) {
            return $this->errorResponse('Please enter Username', 422);
        }
        $user = User::where('email', $data->email)->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }
        $code = mt_rand(100000, 999999);
        $user->update([
            'email_code' => $code,
            'email_code_expire_time' => Carbon::now()->addMinutes(30),
        ]);

        Mail::to($user)->queue(new EmailVerification($user));
        return $this->successResponse('A verification code has been sent to your email');
    }

    public function verifyCode($data)
    {

        if (!$data->email_code) {
            return $this->errorResponse('The Email Code field is required', 422);
        }
        $user = User::where('email_code', $data->email_code)->first();
        if (!$user) {
            return $this->errorResponse('Invalide code entered, please try it again.', 422);
        }

        if ($user->email_code_expire_time < now()) {
            return $this->errorResponse('error', ' Verification Code has Expired!', 422);
        }
        $user->update([
            'email_code' => null,
            'email_code_expire_time' => null,
        ]);
        return $this->successResponse($user);
    }
    public function adminLogin($data)
    {

        $admin = Admin::where('email', $data->email)->first();

        if (!$admin || !Hash::check($data->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate Sanctum token
        $token = $admin->createToken('admin-token')->plainTextToken;
        ActivityLog::createRow($admin->name,ucfirst($admin->name).' Just signed in to the system as an admin');
        return response()->json(['token' => $token, 'admin' => $admin]);
    }

    public function adminLogout()
    {
        request()->user()->tokens()->delete();
        ActivityLog::createRow(request()->user()->name,ucfirst(request()->user()->name).' Just signed out from the system as an admin');
        return response()->json(['message' => 'Logged out successfully']);
    }
}
