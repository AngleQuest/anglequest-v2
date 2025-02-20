<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\AccountService;

class AdminLoginController extends Controller
{
    use ApiResponder;
    public function __construct(
        private AccountService $accountService
    ) {}
    function login(LoginRequest $request)
    {
        return $this->accountService->adminLogin($request);
    }
}
