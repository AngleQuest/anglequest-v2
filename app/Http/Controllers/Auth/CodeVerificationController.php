<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\EmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CodeVerificationController extends Controller
{
    use ApiResponder;

    function verifyCode(Request $request)
    {

        if (!$request->email_code) {
            return $this->errorResponse('The Email Code field is required', 422);
        }
        $user = User::where('email_code',$request->email_code)->first();
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

    function resendCode(Request $request)
    {
        $user = User::where('email', Auth::user() ? Auth::user()->email : $request->email)->first();
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

    function changeEmail(Request $request)
    {

        if (!$request->username) {
            return $this->errorResponse('The username field is required.', 422);
        }
        if (!$request->email) {
            return $this->errorResponse('Please enter the email address you want update.', 422);
        }
        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }

        $user->update([
            'email' => $request->email,
        ]);

        // $user = User::find($user->id);
        // Mail::to($user)->send(new EmailVerification($user));

        return $this->successResponse($user);
    }
}
