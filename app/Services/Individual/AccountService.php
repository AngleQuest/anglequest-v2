<?php

namespace App\Services\Individual;

use Carbon\Carbon;
use App\Models\Hub;
use App\Models\Sla;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Models\UserHub;
use App\Models\UserSla;
use App\Enum\PaymentType;
use App\Mail\NewUserMail;
use App\Enum\AccountStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Models\PaymentHistory;
use App\Models\SupportRequest;
use App\Services\UploadService;
use App\Models\UserSubscription;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\Individual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AccountService
{
    use ApiResponder;

    public function getProfile()
    {
        return $this->successResponse(Auth::user()->profile);
    }

    public function updateProfile($data)
    {
        $user = Auth::user();
        if ($data->file('profile_photo')) {
            $uploadedImage = Cloudinary::upload($data->file('profile_photo')->getRealPath(), [
                'folder' => 'profiles'
            ]);
            $img_url = $uploadedImage->getSecurePath();
        }
        IndividualProfile::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'first_name' => $data->first_name ?? $user->profile->first_name,
                'last_name' => $data->last_name ?? $user->profile->last_name,
                'email' => $data->email ?? $user->profile->email,
                'phone' => $data->phone ?? $user->profile->phone,
                'dob' => $data->dob ?? $user->profile->dob,
                'current_role' => $data->current_role ?? $user->profile->current_role,
                'target_role' => $data->target_role ?? $user->profile->target_role,
                'gender' => $data->gender ?? $user->profile->gender,
                'category' => $data->category ?? $user->profile->category,
                'specialization' => $data->specialization ?? $user->profile->specialization,
                'yrs_of_experience' => $data->yrs_of_experience ?? $user->profile->yrs_of_experience,
                'about' => $data->about ?? $user->profile->about,
                'location' => $data->location ?? $user->profile->location,
                'preferred_mode' => $data->preferred_mode ?? $user->profile->preferred_mode,
                'country' => $data->country ?? $user->profile->country,
                'salary_range' => $data->salary_range ?? $user->profile->salary_range,
                'profile_photo' => $data->profile_photo ? $img_url : $user->profile->profile_photo,
            ]
        );
        $user->update([
            'email' => $data->email
        ]);
        return $this->successResponse('Profile Updated successfully');
    }
    public function updateMode()
    {
        $user = User::find(Auth::user()->id);
        $user->update([
            'mode' => 'closed',
        ]);
        return $this->successResponse('Mode Updated successfully');
    }


    public function updateLoginDetails($data)
    {
        $user = User::find(Auth::id());
        $user->update([
            'email' => $data->email ?? $user->email,
        ]);
        return $this->successResponse($user);
    }

    public function deleteAccount()
    {
        $user = User::find(Auth::id());
        $user->delete();
        $user->tokens()->delete();
        return $this->successResponse('Account Deleted');
    }

    public function blockAccount()
    {
        $user = User::find(Auth::id());
        $user->update([
            'status' => AccountStatus::BLOCKED,
        ]);
        return $this->successResponse('Account Blocked');
    }

    public function updatePassword($data)
    {
        $user = User::find(Auth::id());
        if (Hash::check($data->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($data->password),
            ]);
            return $this->successResponse('Password Successfully Updated');
        }
        return $this->errorResponse(null, 'Old Password did not match', 422);
    }

    public  function getPlans()
    {
        $plans = Plan::latest('id')->where('type', 'individual')->get();
        $sla = Sla::latest('id')->get();
        $data = [
            'plans' => $plans,
            'sla' => $sla,
        ];
        return $this->successResponse($data);
    }

    public function getPaymentHistory()
    {
        $user = Auth::user();
        $histories = PaymentHistory::whereBelongsTo($user)->get();
        return $this->successResponse($histories);
    }

    public function createSubscription($data)
    {
        $plan = Plan::find($data->plan_id);
        if (!$plan) {
            return $this->errorResponse('No record found', 422);
        }
        if ($plan->type == "business") {
            return $this->errorResponse('The Plan selected is not valid for this account', 422);
        }
        $user = Auth::user();
        $payment_id = 'AngleQuest_' . Str::random(10) . $user->id;

        $url = 'https://api.paystack.co/charge';
        $secretKey = 'sk_test_4b7253c5c623e2d3a4b380cf5a6b169d0f63678c'; //env('PAYSTACK_SECRET_KEY');

        $response = Http::withToken($secretKey)->post($url, [
            'email' => $user->email,
            'amount' => $plan->price * 100,
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
                if ($data) {
                    $subscription = UserSubscription::create([
                        'user_id' => $user->id,
                        'subscription_plan_id' => $plan->id,
                        'payment_id' => $payment_id,
                        'plan_start' => now()->toDateString(),
                        'plan_end' => now()->addYear(1),
                        'authorization_data' => $responseBody['data']['authorization'],
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

                        $data = [
                            'subscription_details' => $subscription,
                            'history' => $history,
                        ];
                        return $this->successResponse($data);
                    }
                }
            } catch (\Exception $e) {
                return $e;
            }
            return response()->json([
                'message' => 'Charge successful',
                'data' => $responseBody['data'],
            ]);
        }

        DB::rollBack();
        return response()->json([
            'message' => 'Charge failed',
            'error' => $responseBody['message'],
        ], 400);
    }
    public function subscribeToSla($data)
    {
        $sla = Sla::find($data->sla_id);
        if (!$sla) {
            return $this->errorResponse('No record found', 422);
        }
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $subscription = UserSla::create([
                'user_id' => $user->id,
                'sla_id' => $sla->id,
            ]);
            if ($subscription) {
                DB::commit();

                return $this->successResponse($subscription);
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
        }
    }

    public function submitOtp($data)
    {
        $url = 'https://api.paystack.co/charge/submit_otp';
        $secretKey = 'sk_test_4b7253c5c623e2d3a4b380cf5a6b169d0f63678c'; //env('PAYSTACK_SECRET_KEY');

        $response = Http::withToken($secretKey)->post($url, [
            'otp' => $data->otp,
            'reference' => $data->reference,
        ]);

        $responseBody = $response->json();

        if ($response->successful() && $responseBody['status']) {
            return response()->json([
                'message' => 'Charge completed successfully',
                'data' => $responseBody['data'],
            ]);
        }

        return response()->json([
            'message' => 'OTP submission failed',
            'error' => $responseBody['message'],
        ], 400);
    }
}
