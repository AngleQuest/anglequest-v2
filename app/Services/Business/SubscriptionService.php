<?php

namespace App\Services\Business;

use Carbon\Carbon;
use App\Models\Sla;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Company;
use App\Models\UserSla;
use App\Enum\PaymentType;
use App\Mail\NewUserMail;
use App\Models\AdminBank;
use App\Mail\RenewalEmail;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Resources\PaymentHistoryResource;
use App\Http\Resources\BusinessSubscriptionHistoryResource;
use App\Mail\ActivationEmail;

class SubscriptionService
{
    use ApiResponder;
    public  function getPlans()
    {
        $plans = Plan::latest('id')->where('type', 'business')->get();
        $account_details = AdminBank::first();
        $sla = Sla::latest('id')->get();
        $data = [
            'plans' => $plans,
            'sla' => $sla,
            'account_details' => $account_details
        ];
        return $this->successResponse($data);
    }
    public function getPaymentHistory()
    {
        $user = Auth::user();
        $histories = PaymentHistory::where('user_id', $user->id)->get();
        $data = PaymentHistoryResource::collection($histories);
        return $this->successResponse($data);
    }

    public function store($data)
    {
        $plan = Plan::find($data->plan_id);
        $sla = Sla::find($data->sla_id);
        if (!$sla) {
            return $this->errorResponse('No record found for SLA', 422);
        }
        if (!$plan) {
            return $this->errorResponse('No record found for Plan', 422);
        }
        if ($plan->type == "individual") {
            return $this->errorResponse('The Plan selected is not valid for this account', 422);
        }
        $user = Auth::user();
        $payment_id = 'AQ_' . Str::random(25) . time();
        DB::beginTransaction();
        if ($data) {
            if ($data->payment_method == "transfer") {
                $account_details = AdminBank::first();
                if (!$account_details) {
                    return $this->errorResponse('Admin account is currently empty', 422);
                }
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'payment_id' => $payment_id,
                    'plan_start' => now()->toDateString(),
                    'plan_end' => now()->addYear(1),
                    'authorization_data' => $account_details,
                    'amount' => $plan->price,
                    'plan_name' => $plan->title,
                    'status' => AccountStatus::ACTIVE,
                ]);
                PaymentHistory::create([
                    'user_id' => $user->id,
                    'type' => PaymentType::SUBSCRIPTION,
                    'payment_id' => $payment_id,
                    'plan_id' => $plan->id,
                    'plan_start' => now()->toDateString(),
                    'plan_end' => now()->addYear(1),
                    'amount' => $plan->price,
                    'payment_type' => 'Yearly',
                    'method' => PaymentMethod::TRANSFER,
                    'status' => PaymentStatus::PAID,
                ]);
            }

            if ($data->payment_method == "paystack") {
                $amount = 70000;
                $this->chargeCard($data, $amount, $plan);
            }

            $sla = UserSla::create([
                'user_id' => $user->id,
                'sla_id' => $sla->id
            ]);

            User::where('id', $user->id)->update([
                'plan_id' => $plan->id
            ]);
            DB::commit();
            $detail = [
                'name' => Auth::user()->company->name,
                'service' => str_replace('_', ' ', $plan->title),
            ];
            Mail::to(Auth::user()->email)->send(new ActivationEmail($detail));
            return $this->successResponse('Subscription done Successfully!');
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }

    public function upgrade($data)
    {
        $plan = Plan::find($data->plan_id);
        $sla = Sla::find($data->sla_id);
        if (!$sla) {
            return $this->errorResponse('No record found for SLA', 422);
        }
        if (!$plan) {
            return $this->errorResponse('No record found for Plan', 422);
        }
        if ($plan->type == "individual") {
            return $this->errorResponse('The Plan selected is not valid for this account', 422);
        }
        $user = Auth::user();
        $payment_id = 'AQ_' . Str::random(25) . time();
        DB::beginTransaction();
        if ($data) {
            if ($data->payment_method == "transfer") {
                $account_details = AdminBank::first();
                if (!$account_details) {
                    return $this->errorResponse('Admin account is currently empty', 422);
                }
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'payment_id' => $payment_id,
                    'plan_start' => now()->toDateString(),
                    'plan_end' => now()->addYear(1),
                    'authorization_data' => $account_details,
                    'amount' => $plan->price,
                    'plan_name' => $plan->title,
                    'status' => AccountStatus::ACTIVE,
                ]);
                PaymentHistory::create([
                    'user_id' => $user->id,
                    'type' => PaymentType::UPGRADE,
                    'payment_id' => $payment_id,
                    'plan_id' => $plan->id,
                    'plan_start' => now()->toDateString(),
                    'plan_end' => now()->addYear(1),
                    'amount' => $plan->price,
                    'payment_type' => 'Yearly',
                    'method' => PaymentMethod::TRANSFER,
                    'status' => PaymentStatus::PAID,
                ]);
            }

            if ($data->payment_method == "paystack") {
                $amount = $plan->amount;
                $this->chargeCard($data, $amount, $plan);
            }

            $sla = UserSla::create([
                'user_id' => $user->id,
                'sla_id' => $sla->id
            ]);

            User::where('id', $user->id)->update([
                'plan_id' => $plan->id
            ]);

            DB::commit();
            $detail = [
                'name' => Auth::user()->company->name,
                'service' => str_replace('_', ' ', $plan->title),
            ];
            Mail::to(Auth::user()->email)->send(new RenewalEmail($detail));
            return $this->successResponse('Subscription done Successfully!');
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }


    private function chargeCard($data, $amount, $plan)
    {

        $url = 'https://api.paystack.co/charge';
        $secretKey = 'sk_test_3500fbfb097c5237d9e30fec3c6d56e8dbc3a106'; //env('PAYSTACK_SECRET_KEY');

        $response = Http::withToken($secretKey)->post($url, [
            'email' => $data->email,
            'first_name' => $data->email,
            'last_name' => $data->email,
            'amount' => $amount,
            'card' => [
                'number' => $data->card_number,
                'cvv' => $data->cvv,
                'expiry_month' => $data->expiry_month,
                'expiry_year' => $data->expiry_year,
            ],
        ]);

        $responseBody = $response->json();

        if ($response->successful() && $responseBody['status']) {
            // Check if further authentication is required
            if ($responseBody['data']['status'] === 'send_otp') {
                return response()->json([
                    'message' => 'OTP required',
                    'data' => $responseBody['data'],
                ]);
            }

            DB::beginTransaction();
            try {
                $payment_id = 'AQ_' . Str::random(25) . time();
                UserSubscription::create([
                    'user_id' => Auth::id(),
                    'subscription_plan_id' => $plan->id,
                    'payment_id' => $payment_id,
                    'amount' => $amount,
                    'plan_name' => 'Monthly',
                    'plan_start' => now(),
                    'plan_end' => now()->addMonths(1),
                    'authorization_data' => $responseBody['data']['authorization'],
                    'authorization_code' => $responseBody['data']['authorization']['authorization_code'],
                    'authorization_email' => $data->email,
                    'status' => AccountStatus::ACTIVE,
                ]);

                PaymentHistory::create([
                    'user_id' => Auth::id(),
                    'type' => 'subscription',
                    'amount' => $amount,
                    'payment_type' => 'Monthly',
                    'payment_id' => $payment_id,
                    'plan_id' => $plan->id,
                    'plan_start' => now(),
                    'plan_end' => now()->addMonths(1),
                    'method' => PaymentMethod::PAYSTACK,
                    'status' => PaymentStatus::PAID,
                ]);

                DB::commit();
                return true;
            } catch (\Exception $e) {
                return $e;
                DB::rollBack();
            }
            return response()->json([
                'message' => 'Charge successful',
                'data' => $responseBody['data'],
            ]);
        }

        return response()->json([
            'message' => 'Charge failed',
            'error' => $responseBody['message'],
        ], 400);
    }
}
