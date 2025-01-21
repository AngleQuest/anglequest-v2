<?php

namespace App\Http\Controllers\Business;

use App\Models\User;
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
        $employees = User::where('company_id', $user->company->id)->count();
        $data = [
            'company_name' => $user->company->name,
            'total_employees' => $employees,
        ];
        return $this->successResponse($data);
    }
}
