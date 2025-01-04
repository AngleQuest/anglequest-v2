<?php

namespace App\Http\Controllers\Expert;

use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponder;
    
    function index()
    {
        $user = Auth::user();
        return $this->successResponse($user);
    }
}
