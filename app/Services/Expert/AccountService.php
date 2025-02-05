<?php

namespace App\Services\Expert;

use App\Models\User;
use App\Models\Expert;
use App\Traits\ApiResponder;
use App\Services\UploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    use ApiResponder;

    public function getProfile()
    {
        $user = Auth::user();
        $expert = Expert::whereBelongsTo($user)->first();
        return $this->successResponse($expert);
    }

    public function updateProfile($data)
    {
        $user = Auth::user();
        if ($data->profile_photo) {
            if (File::exists(public_path($user->expert->profile_photo))) {
                File::delete(public_path($user->expert->profile_photo));
            }
            $fileName = str_replace(' ', '', $user->expert->first_name) . '_' . time() . '.' . $data->profile_photo->getClientOriginalExtension();
            $profile_photo = UploadService::upload($data->profile_photo, 'profile', $fileName);
        }

        $expert = Expert::where('user_id', $user->id)->first();
        if (!$expert) return $this->errorResponse('No record matched', 422);
        $expert->update([
            'profile_photo' => $data->profile_photo ? $profile_photo : $user->expert->profile_photo,
            'category' => $data->category ?? $user->expert->category,
            'first_name' => $data->first_name ?? $user->expert->first_name,
            'last_name' => $data->last_name ?? $user->expert->last_name,
            'email' => $data->email ?? $user->expert->email,
            'phone' => $data->phone ?? $user->expert->phone,
            'dob' => $data->dob ?? $user->expert->dob,
            'gender' => $data->gender ?? $user->expert->gender,
            'specialization' => $data->specialization ?? $user->expert->specialization,
            'available_days' => $data->available_days ?? $user->expert->available_days,
            'available_time' => $data->available_time ?? $user->expert->available_time,
            'yrs_of_experience' => $data->yrs_of_experience ?? $user->expert->yrs_of_experience,
            'about' => $data->about ?? $user->expert->about,
            'location' => $data->location ?? $user->expert->location,
        ]);

        return $this->successResponse(Auth::user()->company);
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
        return $this->successResponse('Account Deleted');
    }


    public function updatePassword($data)
    {
        $user = User::find(Auth::id());
        if (!Hash::check($data->old_password, $user->password)) {
            return $this->errorResponse('Old Password does not match', 422);
        }

        $user->update([
            'password' => Hash::make($data->password),
        ]);
        return $this->successResponse('Password Successfully Updated');
    }
}
