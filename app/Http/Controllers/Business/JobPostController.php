<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Requests\JobRequest;
use App\Http\Controllers\Controller;
use App\Services\Business\JobPostService;
use App\Http\Requests\AddCandidateRequest;

class JobPostController extends Controller
{

    public function __construct(
        private JobPostService $jobPostService
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
}
