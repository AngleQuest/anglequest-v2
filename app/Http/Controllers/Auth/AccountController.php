<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Services\CacheService;
use App\Services\Auth\AccountService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AccountController extends Controller
{
    use ApiResponder;
    public function __construct(
        private AccountService $accountService
    ) {}

    public function register(RegisterRequest $request)
    {
        return $this->accountService->signUp($request);
    }

    function login(LoginRequest $request)
    {
        return $this->accountService->login($request);
    }

    public function logout()
    {
        // Revoke the token that was used to authenticate the current request...
        Auth::user()->tokens()->delete();
        return $this->successResponse('user logged out');
    }
}
