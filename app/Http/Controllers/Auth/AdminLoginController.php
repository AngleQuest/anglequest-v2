<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Str;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    use ApiResponder;
    
    function login(Request $request)
    {
        if (!$request->email) {
            return $this->errorResponse('Email field is required', 422);
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Please input a valid email address', 422);
        }
        if (!$request->password) {
            return $this->errorResponse('Password field is required', 422);
        }

        $admin = Admin::where('email', strtolower($request->email))->first();
        if (!$admin) {
            return $this->errorResponse('Oops! No record found with your entry.', 422);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (!Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('Password not matched record', 401);
        }
        $token = Str::random(25) . $admin->id . Str::random(25);
        $exp = Carbon::now()->addDays(1);

        Admin::where('id', $admin->id)->update([
            'api_token' => $token,
        ]);

        $get = Admin::find($admin->id);
        $get['token'] = $token;
        $get['exp'] = $exp;

        return $this->successResponse($get);
    }
}
