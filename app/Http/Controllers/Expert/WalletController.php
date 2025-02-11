<?php

namespace App\Http\Controllers\Expert;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayoutRequest;
use App\Services\Expert\WalletService;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    function index()
    {
        return $this->walletService->history();
    }

    function withdrawFund(PayoutRequest $request)
    {
        return $this->walletService->requestWithdrawal($request);
    }
}
