<?php

namespace App\Services\Business;

use Carbon\Carbon;
use App\Models\Hub;
use App\Models\Sla;
use App\Models\Plan;
use App\Models\User;
use App\Enum\UserRole;
use App\Models\Expert;
use App\Models\Company;
use App\Models\JobPost;
use App\Models\UserHub;
use App\Models\UserSla;
use App\Enum\PaymentType;
use App\Mail\JobPostMail;
use App\Mail\NewUserMail;
use App\Enum\AccountStatus;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Models\ActivityLog;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use App\Mail\OpenAccountEmail;
use App\Models\PaymentHistory;
use App\Models\SupportRequest;
use App\Services\UploadService;
use App\Models\UserSubscription;
use App\Models\IndividualProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\Individual;
use App\Mail\InterviewPaymentMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\Events\JobPopping;
use App\Services\Individual\AppointmentService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class JobPostService
{
    use ApiResponder;

    public function allPosts()
    {
        $posts = JobPost::withCount('appointments')->where('user_id', Auth::user()->id)->latest('id')->get();
        return $this->successResponse($posts);
    }

    public function addPost($data)
    {
        $user = User::find(Auth::id());
        $post = JobPost::create([
            'user_id' =>  Auth::user()->id,
            'category' => $data->category,
            'speacialization' => $data->speacialization,
            'role_level' => $data->role_level,
            'description' => $data->description,
            'job_title' => $data->job_title,
            'candidates' => $data->candidates,
            'link' => 'https://dev.anglequest.com/' . 'job-post/' . str_replace(' ', '', strtolower($user->company->name)) . '.' . str_replace(' ', '-', strtolower($data->job_title)),
        ]);
        return $this->successResponse($post);
    }

    public function editPost($id, $data)
    {
        $post = JobPost::find($id);
        if (!$post) {
            return $this->errorResponse('Job not found', 422);
        }
        $post->update([
            'category' => $data->category,
            'speacialization' => $data->speacialization,
            'role_level' => $data->role_level,
            'description' => $data->description,
            'job_title' => $data->job_title,
        ]);
        return $this->successResponse('Details Updated');
    }

    public function deletePost($id)
    {
        $post = JobPost::find($id);
        if (!$post) {
            return $this->errorResponse('Job not found', 422);
        }
        $post->delete();
        return $this->successResponse('Details Deleted');
    }

    public function viewPost($id)
    {
        $post = JobPost::find($id);
        if (!$post) {
            return $this->errorResponse('Job not found', 422);
        }
        return $this->successResponse($post);
    }

    //Add candidate
    public function addCandidate($data)
    {
        $job = JobPost::find($data->job_id);
        $expert = User::where('role', UserRole::EXPERT)->find($data->expert_id);
        $expert_details = Expert::where('user_id', $data->expert_id)->first();

        if (!$job) {
            return $this->errorResponse('job not found', 422);
        }
        if (!$expert) {
            return $this->errorResponse('Expert not found', 422);
        }
        if ($data->file('cv')) {
            $uploadedImage = Cloudinary::upload($data->file('cv')->getRealPath(), [
                'folder' => 'cvs',
                'resource_type' => 'raw',
                'format' => 'pdf'
            ]);
            $cv = $uploadedImage->getSecurePath();
        }

        DB::beginTransaction();
        try {
            $password = Str::random(5);
            $user = User::create([
                'email' => strtolower($data->email),
                'username' => str_replace(' ', '', $data->email),
                'mode' => 'open',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
                'introducer_id' => Auth::id(),
                'role' => UserRole::INDIVIDUAL
            ]);
            IndividualProfile::create([
                'user_id' => $user->id,
                'first_name' => $data->name,
                'email' => $data->email
            ]);

            //process payment
            $payment = new AppointmentService();
            $payment->chargeCard($data, $user);
            //schedule interview
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'specialization' => $job->specialization,
                'description' => $job->description ?? null,
                'job_description' => $job->job_description ?? null,
                'cv' =>  $cv,
                'is_business' => 1,
                'job_id' => $job->id,
                'role' => $job->role_level,
                'title' => $job->job_title,
                'category' => $job->category,
                'expert_photo' => $expert_details->profile_photo,
                'expert_name' => $expert_details->first_name ? $expert_details->fullName() : $expert->username,
                'individual_name' => $data->name,
                'appointment_date' => $data->appointment_date,
                'appointment_time' => $data->appointment_time,
                'expert_id' => $data->expert_id,
                'status' => 'pending',
            ]);
            if ($user && $appointment) {
                DB::commit();
                $detail = [
                    'name' => $data->name,
                    'amount' => $data->amount,
                    'expert' => $expert_details->first_name ? $expert_details->fullName() : $expert->username,
                ];
                $detail_two = [
                    'email' => $data->email,
                    'name' => $data->username,
                    'password' => $password,
                ];
                ActivityLog::createRow(Auth::user()->username, 'New Account created by ' . ucfirst(Auth::user()->username) . ' for ' . ucfirst($data->name));
                ActivityLog::createRow(Auth::user()->username, 'New Appointment booked by ' . ucfirst(Auth::user()->username) . ' for ' . ucfirst($data->name));
                Mail::to($user->email)->queue(new OpenAccountEmail($detail_two));
                Mail::to($user->email)->queue(new InterviewPaymentMail($detail));

                return response()->json([
                    'status' => 'success',
                    'data' => $appointment,
                ], 200);
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollBack();
        }
    }
}
