<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Requests\JobRequest;
use App\Http\Controllers\Controller;
use App\Services\Business\JobPostService;
use App\Http\Requests\AddCandidateRequest;
use App\Http\Requests\QuestionaireRequest;
use App\Services\Business\QuestionaireService;

class JobPostController extends Controller
{

    public function __construct(
        private JobPostService $jobPostService,
        private QuestionaireService $questionaire
    ) {}

    function index()
    {
        return $this->jobPostService->allPosts();
    }
    function addPost(JobRequest $request)
    {
        return $this->jobPostService->addPost($request);
    }
    function editPost($id, JobRequest $request)
    {
        return $this->jobPostService->editPost($id, $request);
    }
    function viewPost($id)
    {
        return $this->jobPostService->viewPost($id);
    }
    function deletePost($id)
    {
        return $this->jobPostService->deletePost($id);
    }
    function addCandidate(AddCandidateRequest $request)
    {
        return $this->jobPostService->addCandidate($request);
    }

    //Questionaire section
    function allQuestionaires()
    {
        return $this->questionaire->allQuestionaires();
    }
    function addQuestionaire(QuestionaireRequest $request)
    {
        return $this->questionaire->addQuestionaire($request);
    }
    function editQuestionaire($id, QuestionaireRequest $request)
    {
        return $this->questionaire->editQuestionaire($id, $request);
    }
    function viewQuestionaire($id)
    {
        return $this->questionaire->viewQuestionaire($id);
    }
    function deleteQuestionaire($id)
    {
        return $this->questionaire->deleteQuestionaire($id);
    }
}
