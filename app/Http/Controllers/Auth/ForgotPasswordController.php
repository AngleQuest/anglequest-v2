<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\EmailVerification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    use ApiResponder;

    public function verifyUser(Request $request)
    {
        if (!$request->email) {
            return $this->errorResponse('Please enter Username', 422);
        }

        $user = User::where('email', $request->email)->first();

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

    public function verifyCode(Request $request)
    {
        if (!$request->email_code) {
            return $this->errorResponse('The Email Code field is required', 422);
        }
        $user = User::where('email_code', $request->email_code)->first();
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

    public function changePassword(Request $request)
    {
        if (!$request->password) {
            return $this->errorResponse('Password field is required.', 422);
        }

        if (!$request->password_confirmation) {
            return $this->errorResponse('Password firmation field is required.', 422);
        }

        if ($request->password_confirmation != $request->password) {
            return $this->errorResponse('Password confirmation does not match.', 422);
        }

        if (strlen($request->password) < 6) {
            return $this->errorResponse('The password field must be at least 6 characters.', 422);
        }


        $user = User::find($request->user_id);
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return $this->successResponse($user);
    }
    
    function resendCode(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update([
            'email_code' => rand(100000, 999999)
        ]);

        $user = User::find($user->id);

        Mail::to($user)->send(new EmailVerification($user));

        return $this->successResponse('A new code has been sent to you');
    }
}
