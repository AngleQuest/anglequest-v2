<?php

namespace App\Services\Expert;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Payout;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\UserWallet;
use App\Models\Appointment;
use App\Models\IncomeWallet;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Models\Configuration;
use App\Models\SupportRequest;
use App\Mail\EmailVerification;
use App\Models\UserPaymentInfo;
use Illuminate\Support\Facades\DB;
use App\Models\AppointmentFeedback;
use Illuminate\Support\Facades\Auth;

class InterviewService
{
    use ApiResponder;
    public function history()
    {
        $user = Auth::user();
        $wallet = UserWallet::where('user_id', $user->id)->first();
        $all_earnings = IncomeWallet::where('user_id', $user->id)->sum('amount');
        $total_withdrawn = Payout::where('user_id', $user->id)->where('status', 'paid')->sum('amount');
        $transactions =  IncomeWallet::where('user_id', $user->id)->latest('id')->get();
        $data = [
            'balance' => $wallet->master_wallet,
            'all_earnings' => $all_earnings,
            'withdrawn' => $total_withdrawn,
            'transactions' => $transactions
        ];

        return $this->successResponse($data);
    }

    public function requestWithdrawal($data)
    {
        $user = Auth::user();
            $wallet = UserWallet::where('user_id', $user->id)->first();

            if ($data->amount > $wallet->master_wallet) {
                 return $this->errorResponse('Insufficient balance', 422);
            }

            $paymentInfo = UserPaymentInfo::where('user_id', $user->id)->first();

            if (!$paymentInfo || !$paymentInfo->account_name) {
                return $this->errorResponse('Please update your payment info', 422);
            }

            $pendingRequest = Payout::where(['user_id' => $user->id, 'status' => 'pending'])->count();
            if ($pendingRequest) {
                return $this->errorResponse('Opps! You have pending payout request, please wait for approval.',422);
            }

            DB::beginTransaction();

            $payout = Payout::create([
                'user_id' => $user->id,
                'amount' => $data->amount,
                'account_name' => $paymentInfo->account_name,
                'account_number' => $paymentInfo->account_number,
                'bank' => $paymentInfo->bank_name,
            ]);

            $wallet->master_wallet -= $data->amount;

            if ($wallet->save() && $payout) {
                DB::commit();
                return $this->successResponse('Payout request submitted successfully');
            }

            DB::rollBack();
            return $this->errorResponse('Opps! Something went wrong, your request could not be processed',422);
        }

}
