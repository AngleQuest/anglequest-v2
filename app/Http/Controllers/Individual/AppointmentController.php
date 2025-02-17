<?php

namespace App\Http\Controllers\Individual;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\AppointmentMergeRequest;
use App\Services\Individual\AppointmentService;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

    function bookAppointment(AppointmentRequest $request)
    {

        return $this->appointmentService->bookAppointment($request);
    }
    function sendCV(AppointmentRequest $request)
    {
        return $this->appointmentService->sendCV($request);
    }
    
    function mergeAppointment(AppointmentMergeRequest $request)
    {
        return $this->appointmentService->mergeAppointment($request);
    }


    function declinedAppointments()
    {
        return $this->appointmentService->declinedAppointments();
    }

    function completedAppointments()
    {
        return $this->appointmentService->completedAppointments();
    }

    function feedback($id)
    {
        return $this->appointmentService->appointmentFeedback($id);
    }
}
