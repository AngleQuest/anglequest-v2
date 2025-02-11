<?php

namespace App\Http\Controllers\Expert;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Expert\InterviewService;
use App\Http\Requests\InterviewGuideRequest;

class InterviewManagerController extends Controller
{
    public function __construct(
        private InterviewService $interviewService
    ) {}

    function viewAppointment($id)
    {
        return $this->interviewService->viewAppointment($id);
    }

    function acceptAppointment($id)
    {

        return $this->interviewService->acceptAppointment($id);
    }

    function rejectAppointment($id)
    {

        return $this->interviewService->rejectAppointment($id);
    }

    function createGuide(InterviewGuideRequest $request)
    {
        return $this->interviewService->createGuide($request);
    }
    function viewGuide()
    {
        return $this->interviewService->viewGuide();
    }

    function pendingAppointments()
    {
        return $this->interviewService->pendingAppointments();
    }
    function acceptedAppointments()
    {
        return $this->interviewService->acceptedAppointments();
    }
    function completedAppointments()
    {
        return $this->interviewService->completedAppointments();
    }
    function declinedAppointments()
    {
        return $this->interviewService->declinedAppointments();
    }
    
    function createFeedback($id, Request $request)
    {
        return $this->interviewService->appointmentFeedback($id, $request);
    }
}
