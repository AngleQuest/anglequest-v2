<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\AccountService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    use ApiResponder;
    public function __construct(
        private AccountService $accountService
    ) {}

    public function register(Request $request)
    {
        CacheService::removeAll();
        $email = User::where('email', $request->email)->first();

        if (!$request->email) {
            return $this->errorResponse('Email field is required', 422);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Please input a valid email address', 422);
        }
        if ($email) {
            return $this->errorResponse('Email already taken', 422);
        }
        if (!$request->password) {
            return $this->errorResponse('Password field is required', 422);
        }

        if (strlen($request->password) < 6) {
            return $this->errorResponse('The password field must be at least 6 characters', 422);
        }
        if (!$request->password_confirmation) {
            return $this->errorResponse('Password confirmation field is required', 422);
        }

        if ($request->password_confirmation != $request->password) {
            return $this->errorResponse('Password confirmation does not match', 422);
        }

        $user = $this->accountService->signUp($request);

        if ($user) {
            return $this->successResponse($user);
        }

        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }

    function login(Request $request)
    {
        if (!$request->email) {
            return $this->errorResponse('Email field is required', 422);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Please input a valid email address', 422);
        }
        if (!$request->password) {
            return $this->errorResponse('Password field is required', 422);
        }

        $user = User::where('email', strtolower($request->email))->first();
        if (!$user) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

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
}
