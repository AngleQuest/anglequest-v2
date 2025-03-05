<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Mail\OpenAccountEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ExpertManagerService
{
    use ApiResponder;

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
        $expert =  Expert::find($id);
        if (!$expert) {
            return $this->errorResponse('No record found', 422);
        }
        return $this->successResponse($expert);
    }
    public function updateExpert($id, $data)
    {
        $expert =  Expert::find($id);
        if (!$expert) {
            return $this->errorResponse('No record found', 422);
        }
        $user =  User::where('id', $expert->user_id)->first();
        if (!$user) {
            return $this->errorResponse('User not found', 422);
        }
        $expert->update([
            'category' => $data->category ?? $expert->category,
            'name' => $data->name ?? $expert->name,
            'email' => $data->email ?? $expert->email,
            'phone' => $data->phone ?? $expert->phone,
            'specialization' => $data->specialization ?? $expert->specialization,
            'available_days' => $data->available_days ?? $expert->available_days,
            'about' => $data->about ?? $expert->about,
            'location' => $data->location ?? $expert->location,
        ]);
        $user->update([
            'email' => $data->email
        ]);
        return $this->successResponse($expert);
    }

    public function newExpert($data)
    {
        DB::beginTransaction();
        if ($data) {
            $password = Str::random(6);
            $user = User::create([
                'email' => strtolower($data->email),
                'mode' => 'open',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
                'role' => UserRole::EXPERT
            ]);
            Expert::create([
                'user_id' => $user->id,
                'name' => $data->name,
                'email' => $data->email
            ]);

            if ($user) {
                DB::commit();
                $detail = [
                    'email' => $data->email,
                    'password' => $password,
                ];
                ActivityLog::createRow($user->email, 'New Account created by Admin');
                Mail::to($user->email)->queue(new OpenAccountEmail($detail));
                return $this->successResponse($user);
            }
        }

        DB::rollBack();
        return $this->errorResponse('Opps! Something went wrong, your request could not be processed', 422);
    }
    public function deleteExpert($id)
    {
        $expert =  Expert::find($id);
        if (!$expert) {
            return $this->errorResponse('Expert not found', 422);
        }
        ActivityLog::createRow('Admin', 'Admin Deleted ' . $expert->email . ' Account');
        User::where('id', $expert->user_id)->delete();
        $expert->delete();
        return $this->successResponse('Account deleted');
    }
}
