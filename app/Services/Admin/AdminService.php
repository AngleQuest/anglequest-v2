<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Payout;
use App\Models\Company;
use App\Enum\UserStatus;
use App\Models\AdminBank;
use App\Models\UserWallet;
use App\Enum\PaymentStatus;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Traits\ApiResponder;
use App\Models\Configuration;
use App\Models\IndividualProfile;
use App\Http\Middleware\Individual;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\IndividualResource;

class AdminService
{
    use ApiResponder;
    public function getDashboardData()
    {
        $active_interviews = Appointment::where('status', UserStatus::ACTIVE)->count();
        $total_individuals = User::where('role', UserRole::INDIVIDUAL)->count();
        $total_experts = User::where('role', UserRole::EXPERT)->count();
        $total_companies = User::where('role', UserRole::BUSINESS)->count();
        $recent_users = User::select(['username', 'email', 'role', 'created_at'])->latest('id')->take(10)->get();
        $activity_log = ActivityLog::latest('id')->take(10)->get();

        $data = [
            'active_interviews' => $active_interviews,
            'total_individuals' => $total_individuals,
            'total_experts' => $total_experts,
            'total_companies' => $total_companies,
            'recent_users' => $recent_users,
            'activity_log' => $activity_log,
        ];
        return $this->successResponse($data);
    }
    public function getAccountDetails()
    {
        $admin_bank =  AdminBank::first();
        if (!$admin_bank) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($admin_bank);
    }

    public function getConfigDetails()
    {
        $configuration = Configuration::first();
        if (!$configuration) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($configuration);
    }

    public  function updateConfigDetails($data)
    {
        $configuration =  Configuration::first();
        if ($configuration) {
            $configuration->update([
                'usd_rate' => $data->usd_rate,
                'email_verify' => $data->email_verify,
                'currency_code' => $data->currency_code,
                'currency_symbol' => $data->currency_symbol,
                'withdrawal_min' => $data->withdrawal_min,
                'withdrawal_max' => $data->withdrawal_max,
                'expert_fee' => $data->expert_fee,
                'africa_fee' => $data->africa_fee,
                'asia_fee' => $data->asia_fee,
                'europe_fee' => $data->europe_fee,
            ]);
        }
        $configuration = Configuration::create([
            'usd_rate' => $data->usd_rate,
            'email_verify' => $data->email_verify,
            'currency_code' => $data->currency_code,
            'currency_symbol' => $data->currency_symbol,
            'withdrawal_min' => $data->withdrawal_min,
            'withdrawal_max' => $data->withdrawal_max,
            'expert_fee' => $data->expert_fee,
            'africa_fee' => $data->africa_fee,
            'asia_fee' => $data->asia_fee,
            'europe_fee' => $data->europe_fee,
        ]);
        return $this->successResponse($configuration);
    }

    public  function updateDetails($data)
    {
        $admin_bank =  AdminBank::first();
        if ($admin_bank) {
            $admin_bank->update([
                'account_name' => $data->account_name,
                'account_number' => $data->account_number,
                'bank_name' => $data->bank_name,
                'country' => $data->country,
            ]);
        }
        $admin_bank = AdminBank::create([
            'account_name' => $data->account_name,
            'account_number' => $data->account_number,
            'bank_name' => $data->bank_name,
            'country' => $data->country,
        ]);
        return $this->successResponse($admin_bank, 200);
    }

    public function getCompanies()
    {
        $companies =  Company::latest('id')->get();
        if (!$companies) {
            return $this->errorResponse('No record found', 404);
        }
        $data = CompanyResource::collection($companies);
        return $this->successResponse($data);
    }
    public function getSingleCompany($id)
    {
        $company =  Company::find($id);
        if (!$company) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($company);
    }
    public function getUsers()
    {
        $users =  User::where('role', '!=', UserRole::BUSINESS)->latest('id')->get();
        if (!$users) {
            return $this->errorResponse('No record found', 404);
        }
        $data = UserResource::collection($users);
        return $this->successResponse($data);
    }
    public function deActivateUser($id)
    {
        $user =  User::find($id);
        if (!$user) {
            return 'User not found';
        }
        $user->update([
            'status' => UserStatus::BLOCKED
        ]);
        return $this->successResponse('Account de-activated');
    }

    public function getIndividuals()
    {
        $individuals =  IndividualProfile::with('user')->latest('id')->get();
        if (!$individuals) {
            return $this->errorResponse('No record found', 404);
        }
        $data = IndividualResource::collection($individuals);
        return $this->successResponse($data);
    }



    public function getSingleIndividual($id)
    {
        $individual =  IndividualProfile::with('user')->find($id);
        if (!$individual) {
            return $this->errorResponse('No record found', 404);
        }
        $data = new IndividualResource($individual);
        return $this->successResponse($data);
    }

    public function getExperts()
    {
        $experts =  Expert::latest('id')->get();
        if (!$experts) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($experts);
    }
    public function getSingleExpert($id)
    {
        $expert =  Expert::with('user')->find($id);
        if (!$expert) {
            return $this->errorResponse('No record found', 404);
        }
        return $this->successResponse($expert);
    }
    public function withdrawalRequests()
    {
        $payouts =  Payout::with('user')->latest('id')->get();
        return $this->successResponse($payouts);
    }
    public function approveRequest($id)
    {
        $payout = Payout::findOrFail($id);
        $payout->update([
            'status' => PaymentStatus::PAID,
            'date_paid' => Carbon::now()->toDateString()

        ]);
        return $this->successResponse('Request Approved');
    }
    public function declineRequest($id)
    {
        $payout = Payout::findOrFail($id);
        $user = User::findOrFail($payout->user_id);
        // $wallet = UserWallet::where('user_id',$payout->user_id)->first();
        // if ($wallet) {
        //   $wallet->master_wallet+=$payout->amount;
        //   $payout->save();
        // }
        $wallet = $user->wallet()->firstOrCreate([
            'user_id' => $user->id
        ]);

        $wallet->increment('master_wallet', $payout->amount);
        $payout->update([
            'status' => PaymentStatus::UNPAID,
        ]);
        return $this->successResponse('Request Declined');
    }
}
