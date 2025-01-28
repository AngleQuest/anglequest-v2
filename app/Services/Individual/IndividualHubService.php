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
        //$user = Individual::where('user_', $data->specialization)->first();
        return $profile = Auth::user()->individualProfile;
    }
    public function attachHub($id)
    {
        $hub = Hub::find($id);
        if (!$hub) {
            return $this->errorResponse(null, 'No record found');
        }
        if ($hub->hub_count == 20) {
            return $this->errorResponse(null, 'Hub Threshold  reached');
        }
        $hub_detais = UserHub::create([
            'user_id' => Auth::id(),
            'hub_id' => $hub->id,
            'expert_id' => $hub->user_id,
            'hub_count' => $hub->hub_count += 1,
        ]);
        return $this->successResponse($hub_detais);
    }
    public function leaveHub($id)
    {
        $hub = Hub::find($id);
        if (!$hub) {
            return $this->errorResponse(null, 'No record found');
        }
        if ($hub->hub_count == 20) {
            return $this->errorResponse(null, 'Hub Threshold  reached');
        }
        $hub_detais = UserHub::where([
            'user_id' => Auth::id(),
            'hub_id' => $hub->id
        ]);
        $hub_detais->delete();
        return $this->successResponse($hub_detais);
    }
}
