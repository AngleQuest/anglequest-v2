<?php

namespace App\Services\Expert;

use App\Models\User;
use App\Models\Expert;
use App\Models\ExpertExperience;
use App\Models\UserPaymentInfo;
use App\Traits\ApiResponder;
use App\Services\UploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        if ($data->file('profile_photo')) {
            $uploadedImage = Cloudinary::upload($data->file('profile_photo')->getRealPath(), [
                'folder' => 'profiles'
            ]);
            $profile_photo = $uploadedImage->getSecurePath();
        }

        $expert = Expert::where('user_id', $user->id)->first();
        if (!$expert) return $this->errorResponse('No record matched', 422);
        $expert->update([
            'profile_photo' => $data->profile_photo ? $profile_photo : $expert->profile_photo,
            'category' => $data->category ?? $expert->category,
            'first_name' => $data->first_name ?? $expert->first_name,
            'last_name' => $data->last_name ?? $expert->last_name,
            'email' => $data->email ?? $expert->email,
            'phone' => $data->phone ?? $expert->phone,
            'dob' => $data->dob ?? $expert->dob,
            'gender' => $data->gender ?? $expert->gender,
            'specialization' => $data->specialization ?? $expert->specialization,
            'available_days' => $data->available_days ?? $expert->available_days,
            'about' => $data->about ?? $expert->about,
            'location' => $data->location ?? $expert->location,
        ]);

        return $this->successResponse('Details Updated');
    }
    public function createPaymentInfo($data)
    {
        $user = Auth::user();
        UserPaymentInfo::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'account_name' => $data->account_name,
                'account_number' => $data->account_number,
                'bank' => $data->bank,
                'country' => $data->country,
            ]
        );
        return $this->successResponse('Payment details Updated successfully');
    }

    public function getPaymentInfo()
    {
        $user = Auth::user();
        $payment_info = UserPaymentInfo::where('user_id', $user->id)->first();
        if (!$payment_info) {
            return $this->errorResponse('No payment details added', 422);
        }
        return $this->successResponse($payment_info);
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

    //Job Experiences
    public function getExperiences()
    {
        $user = Auth::user();
        $experiences = ExpertExperience::where('user_id', $user->id)->latest('id')->get();
        return $this->successResponse($experiences);
    }
    public function addExperience($data)
    {
        $user = Auth::user();
        ExpertExperience::create(
            [
                'user_id' => $user->id,
                'company_name' => $data->company_name,
                'position' => $data->position,
                'from' => $data->start_date,
                'to' => $data->end_date,
            ]
        );
        return $this->successResponse('details added successfully');
    }
    public function editExperience($id)
    {
        $experience = ExpertExperience::find($id);
        if (!$experience) {
            return $this->errorResponse('No rrecord found', 422);
        }
        return $this->successResponse($experience);
    }
    public function updateExperience($id, $data)
    {
        $experience = ExpertExperience::find($id);
        if (!$experience) {
            return $this->errorResponse('No rrecord found', 422);
        }
        $experience->update([
            'company_name' => $data->company_name,
            'position' => $data->position,
            'from' => $data->start_date,
            'to' => $data->end_date,
        ]);
        return $this->successResponse('details updated successfully');
    }
    public function removeExperience($id)
    {
        $experience = ExpertExperience::find($id);
        if (!$experience) {
            return $this->errorResponse('No rrecord found', 422);
        }
        $experience->delete();
        return $this->successResponse('details deleted successfully');
    }
}
