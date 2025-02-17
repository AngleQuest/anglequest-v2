<?php

namespace App\Services\Individual;

use Carbon\Carbon;
use App\Models\User;
use App\Enum\UserRole;
use App\Http\Middleware\Individual;
use App\Models\Expert;
use App\Models\Company;
use App\Mail\NewUserMail;
use App\Models\Hub;
use App\Traits\ApiResponder;
use App\Models\SupportRequest;
use App\Models\UserHub;
use Illuminate\Support\Facades\Auth;

class IndividualHubService
{
    use ApiResponder;


    public function getAreaHub()
    {
        $profile = Auth::user()->profile;
        $hubs = Hub::where('category', $profile->category)->get();
        return $this->successResponse($hubs);
    }
    public function attachHub($id)
    {
        $hub = Hub::find($id);
        $user = User::find(Auth::id());
        $check = UserHub::where(['user_id' => $user->id, 'hub_id' => $hub->id])->first();
        $count = UserHub::all()->count();
        if (!$hub) {
            return $this->errorResponse('No record found', 404);
        }
        if ($count == 20) {
            return $this->errorResponse('Hub Threshold  reached', 422);
        }
        if ($check) {
            return $this->errorResponse('Already a member', 422);
        }

        $user->myHub()->attach($hub);
        return $this->successResponse('User attached to Hub');
    }
    public function leaveHub($id)
    {
        $hub = Hub::find($id);
        $user = User::find(Auth::id());
        $check = UserHub::where(['user_id' => $user->id, 'hub_id' => $hub->id])->first();
        if (!$hub) {
            return $this->errorResponse('No record found', 404);
        }
        if (!$check) {
            return $this->errorResponse('No hub found for user', 404);
        }
        $user->myHub()->detach($hub);
        return $this->successResponse('User detached from Hub');
    }
}
