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
use App\Models\Questionaire;
use App\Traits\ApiResponder;
use App\Mail\OpenAccountEmail;
use App\Models\PaymentHistory;
use App\Models\SupportRequest;
use App\Services\UploadService;
use App\Models\UserSubscription;
use App\Models\IndividualProfile;
use App\Mail\InterviewPaymentMail;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\Individual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\Events\JobPopping;
use App\Services\Individual\AppointmentService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class QuestionaireService
{
    use ApiResponder;

    public function allQuestionaires()
    {
        $questionaires = Questionaire::where('user_id', Auth::user()->id)->latest('id')->get();
        return $this->successResponse($questionaires);
    }

    public function addQuestionaire($data)
    {
        $questionaire = Questionaire::create([
            'user_id' =>  Auth::user()->id,
            'category' => $data->category,
            'title' => $data->title,
            'question_answer' => $data->question_answer,
            'answer' => $data->answer,
        ]);
        return $this->successResponse($questionaire);
    }
    // "category:SAP
    // "specialization:SAP Fi
    // "role_level:Junior
    // "description:SAP Specialist
    // "job_title:SAP Specialist
    // "candidates:10

    public function editQuestionaire($id, $data)
    {
        $questionaire = Questionaire::find($id);
        if (!$questionaire) {
            return $this->errorResponse('questionaire not found', 422);
        }
        $questionaire->update([
            'category' => $data->category,
            'title' => $data->title,
            'question_answer' => $data->question_answer,
            'answer' => $data->answer,
        ]);
        return $this->successResponse('Details Updated');
    }

    public function deleteQuestionaire($id)
    {
        $questionaire = Questionaire::find($id);
        if (!$questionaire) {
            return $this->errorResponse('questionaire not found', 422);
        }
        $questionaire->delete();
        return $this->successResponse('Details Deleted');
    }

    public function viewQuestionaire($id)
    {
        $questionaire = Questionaire::find($id);
        if (!$questionaire) {
            return $this->errorResponse('Questionaire not found', 422);
        }
        return $this->successResponse($questionaire);
    }
}
