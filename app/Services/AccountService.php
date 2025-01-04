<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\UserWallet;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\ProductRating;
use App\Models\ProductReview;
use App\Mail\EmailVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountService
{
    use ApiResponder;
    public static function signUp($data)
    {
        $user = '';
        DB::beginTransaction();
        if ($data) {
            $config = Configuration::first();
            $code = mt_rand(100000, 999999);
            if ($data->role == "individual") {
                $user = User::create([
                    'username' => $data->username,
                    'email' => strtolower($data->email),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
            }
            if ($data->role == "expert") {
                $user = User::create([
                    'username' => $data->username,
                    'email' => strtolower($data->email),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
                UserWallet::create([
                    'user_id' => $user->id
                ]);
            }
            if ($data->role == "business") {
                $company = Company::create([
                    'name' => $data->company_name,
                    'administrator_name' => $data->administrator_name,
                    'email' => $data->email,
                ]);
                $user = User::create([
                    'username' => $data->email,
                    'company_id' => $company->id,
                    'email' => strtolower($data->email),
                    'password' => Hash::make($data->password),
                    'role' => $data->role
                ]);
            }
            
            DB::commit();
            if ($user) {
                if (strtolower($config->email_verify) == "enabled") {
                    $user->update([
                        'email_code' => $code,
                        'email_code_expire_time' => Carbon::now()->addMinutes(30),
                    ]);
                    Mail::to($user->email)->queue(new EmailVerification($user));
                } else {
                    $user->update(['email_verified_at' => Carbon::now()->toDateTimeString()]);
                    Mail::to($user->email)->queue(new NewUserMail($user));
                }
                $user->token = $user->createToken($user->username . 'API Token')->plainTextToken;
                return $user;
            }
        }

        DB::rollBack();
        return 'Opps! Something went wrong, your request could not be processed';
    }
}
