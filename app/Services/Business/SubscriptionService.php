<?php

namespace App\Services\Business;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Company;
use App\Enum\PaymentType;
use App\Mail\NewUserMail;
use App\Models\AdminBank;
use App\Enum\AccountStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Mail\EmailInvitation;
use App\Models\PaymentHistory;
use App\Mail\EmailVerification;
use App\Models\UserSubscription;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubscriptionService
{
    use ApiResponder;
    public  function getPlans()
    {
        $plans = Plan::latest('id')->where('type','business')->get();
        $account_details = AdminBank::first();
        $data = [
            'plans' => $plans,
            'account_details' => $account_details
        ];
        return $this->successResponse($data);
    }
    public function getPaymentHistory()
    {
         $user = Auth::user();
        $histories = PaymentHistory::whereBelongsTo($user)->get();
        return $this->successResponse($histories);
    }

    public function store($data)
    {
        $plan = Plan::find($data->plan_id);
        if (!$plan) {
            return $this->errorResponse('No record found',422);
        }
        if ($plan->type == "individual") {
            return $this->errorResponse('The Plan selected is not valid for this account',422);
        }
        $user = Auth::user();
        $payment_id = 'AngleQuest_' . Str::random(10) . $user->id;
        DB::beginTransaction();
        if ($data) {
            $account_details = AdminBank::first();
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'payment_id' => $payment_id,
                'plan_start' => now()->toDateString(),
                'plan_end' => now()->addYear(1),
                'authorization_data' => $account_details,
                'status' => AccountStatus::ACTIVE,
            ]);
            $history = PaymentHistory::create([
                'user_id' => $user->id,
                'type' => PaymentType::SUBSCRIPTION,
                'payment_id' => $payment_id,
                'plan_id' => $plan->id,
                'plan_start' => now()->toDateString(),
                'plan_end' => now()->addYear(1),
                'amount' => $plan->price,
                'method' => PaymentMethod::TRANSFER,
                'status' => PaymentStatus::PAID,
            ]);
            if ($subscription && $history) {
                DB::commit();
                // $detail = [
                //     'name' => $user->company->name,
                //     'email' => $employee->email,
                //     'password' => 'password12345'
                // ];
                // Mail::to($employee->email)->send(new EmailInvitation($detail));
                $data = [
                    'subscription_details' => $subscription,
                    'history' => $history,
                ];
                return $this->successResponse($data);
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
}
