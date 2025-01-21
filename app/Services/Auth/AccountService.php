<?php

namespace App\Services\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\UserWallet;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\ProductRating;
use App\Models\ProductReview;
use App\Mail\EmailVerification;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountService
{
    use ApiResponder;

    public  function signUp($data)
    {
        $user = '';
        DB::beginTransaction();
        if ($data) {
            $code = mt_rand(100000, 999999);
            if ($data->role == UserRole::INDIVIDUAL) {
                $user = User::create([
                    'email' => strtolower($data->email),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
                IndividualProfile::create([
                    'user_id' => $user->id
                ]);
            }
            if ($data->role == UserRole::EXPERT) {
                $user = User::create([
                    'email' => strtolower($data->email),
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
                $company = Company::create([
                    'name' => $data->company_name,
                    'administrator_name' => $data->administrator_name,
                    'email' => $data->email,
                ]);
                $user = User::create([
                    'company_id' => $company->id,
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
                return $user;
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

        $credentials = ['email' => $data->email, 'password' => $data->password];

        if (!Auth::attempt($credentials)) {
            return $this->errorResponse('Credentials inputted do not match, please try it again.', 422);
        }

        if (strtolower($user->status) == 'blocked' || strtolower($user->status) == 'suspended') {
            return $this->errorResponse('This account has been Blocked / Suspended. Please Contact support for activation.', 422);
        }

        //event(new Login($user));
        $user->token = $user->createToken($user->email . ' Login Token')->plainTextToken;

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
        return $this->successResponse($user);
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
}
