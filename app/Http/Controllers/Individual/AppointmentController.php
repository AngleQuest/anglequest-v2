<?php

namespace App\Http\Controllers\Individual;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Services\Individual\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {}

    function bookAppointment(AppointmentRequest $request)
    {
        return $this->appointmentService->bookAppointment($request);
    }
    function storeAppointment(AppointmentRequest $request)
    {
        return $this->appointmentService->storeAppointment($request);
    }
    function makePayment(Request $request)
    {
        return $this->appointmentService->bookAppointment($request);
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
