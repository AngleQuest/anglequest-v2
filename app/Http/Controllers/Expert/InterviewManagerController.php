<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Services\Expert\InterviewService;
use Illuminate\Http\Request;

class InterviewManagerController extends Controller
{
    public function __construct(
        private InterviewService $interviewService
    ) {}

    function acceptAppointment($id)
    {

        return $this->interviewService->acceptAppointment($id);
    }
    function index()
    {
        return $this->interviewService->allAppointments();
    }
}
