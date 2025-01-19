<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\NewUserMail;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Mail\EmailVerification;
use App\Services\AccountService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CodeVerificationController extends Controller
{
    use ApiResponder;
    public function __construct(
        private AccountService $accountService
    ) {}
    function verifyCode(Request $request)
    {
        return $this->accountService->emailVerification($request);
    }

    function resendCode(Request $request)
    {
        return $this->accountService->resendCode($request);
    }
}
