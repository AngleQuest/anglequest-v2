<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Enum\UserLog;
use App\Trait\SignUp;
use App\Enum\UserType;
use App\Models\Coupon;
use App\Models\Country;
use App\Enum\UserStatus;
use App\Trait\HttpResponse;
use App\Traits\ApiResponder;
use App\Mail\UserWelcomeMail;
use App\Mail\SignUpVerifyMail;
use App\Actions\SendEmailAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthServices extends Controller
{
    use ApiResponder;

    public function AuthLogin($data)
    {
        if (!$data->password) {
            return $this->errorResponse('Password field is required', 422);
        }

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
    public function loginVerify($request)
    {
        $user = User::where('email', $request->email)
            ->where('login_code', $request->code)
            ->where('login_code_expires_at', '>', now())
            ->first();

        if (! $user) {
            return $this->error(null, "User doesn't exist or Code has expired.", 404);
        }

        $user->update([
            'login_code' => null,
            'login_code_expires_at' => null
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('API Token of ' . $user->email);

        $description = "User with email {$request->email} logged in";
        $action = UserLog::LOGGED_IN;
        $response = $this->success([
            'user_id' => $user->id,
            'user_type' => $user->type,
            'has_signed_up' => true,
            'is_affiliate_member' => $user->is_affiliate_member === 1 ? true : false,
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ]);

        logUserAction($request, $action, $description, $response, $user);

        return $response;
    }

    public function signup($request)
    {
        $request->validated($request->all());

        try {
            $user = $this->createUser($request);

            if ($referrer = $request->query('referrer')) {
                $this->handleReferrer($referrer, $user);
            }

            $description = "User with email: {$request->email} signed up";
            $response = $this->success(null, "Created successfully");
            $action = UserLog::CREATED;

            logUserAction($request, $action, $description, $response, $user);

            return $response;
        } catch (\Exception $e) {
            $description = "Sign up failed: {$request->email}";
            $response = $this->error(null, $e->getMessage(), 500);
            $action = UserLog::FAILED;

            logUserAction($request, $action, $description, $response, $user);

            return $response;
        }
    }

    public function resendCode($request)
    {
        $user = User::getUserEmail($request->email);

        if (!$user) {
            return $this->error(null, "User not found", 404);
        }

        if ($user->email_verified_at !== null && $user->status === UserStatus::ACTIVE) {
            return $this->error(null, "Account has been verified", 400);
        }

        try {

            $code = generateVerificationCode();

            $user->update([
                'email_verified_at' => null,
                'verification_code' => $code,
            ]);

            defer(fn() => send_email($request->email, new SignUpVerifyMail($user)));

            $description = "User with email address {$request->email} has requested a code to be resent.";
            $action = UserLog::CODE_RESENT;
            $response = $this->success(null, "Code resent successfully");

            logUserAction($request, $action, $description, $response, $user);

            return $response;
        } catch (\Exception $e) {
            $description = "An error occured during the request email: {$request->email}";
            $action = UserLog::FAILED;
            $response = $this->error(null, $e->getMessage(), 500);

            logUserAction($request, $action, $description, $response, $user);
            return $response;
        }
    }

    public function sellerSignup($request)
    {
        $request->validated($request->all());
        $user = null;

        $currencyCode = 'NGN';
        if ($request->country_id) {
            $country = Country::findOrFail($request->country_id);
            $currencyCode = getCurrencyCode($country->sortname);
        }

        try {
            $code = generateVerificationCode();

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middlename' => $request->other_name,
                'email' => $request->email,
                'address' => $request->address,
                'country' => $request->country_id,
                'state_id' => $request->state_id,
                'type' => UserType::SELLER,
                'default_currency' => $currencyCode,
                'email_verified_at' => null,
                'verification_code' => $code,
                'is_verified' => 0,
                'password' => bcrypt($request->password)
            ]);

            if ($coupon = $request->query('coupon')) {
                $this->validateAndAssignCoupon($coupon, $user);
            }

            $description = "Seller with email address {$request->email} just signed up";
            $action = UserLog::CREATED;
            $response = $this->success(null, "Created successfully");

            logUserAction($request, $action, $description, $response, $user);

            return $this->success(null, "Created successfully");
        } catch (\Exception $e) {
            $description = "Sign up error for user with email {$request->email}";
            $action = UserLog::FAILED;
            $response = $this->error(null, $e->getMessage(), 500);

            logUserAction($request, $action, $description, $response, $user);

            return $response;
        }
    }

    public function verify($request)
    {
        $user = User::where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();

        if (!$user) {
            return $this->error(null, "Invalid code", 404);
        }

        $user->update([
            'is_verified' => 1,
            'is_admin_approve' => 1,
            'verification_code' => null,
            'email_verified_at' => now(),
            'status' => UserStatus::ACTIVE
        ]);

        (new SendEmailAction($user->email, new UserWelcomeMail($user)))->run();

        $description = "User with email address {$request->email} verified OTP";
        $action = UserLog::CREATED;
        $response = $this->success(null, "Verified successfully");

        logUserAction($request, $action, $description, $response, $user);

        return $response;
    }


    public function logout()
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        $description = "User with email address {$user->email} logged out";
        $action = UserLog::LOGOUT;
        $response = $this->success([
            'message' => 'You have successfully logged out and your token has been deleted'
        ]);

        logUserAction(request(), $action, $description, $response, $user);

        return $response;
    }
}
