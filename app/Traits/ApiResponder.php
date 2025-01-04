<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

trait ApiResponder
{

	/**
	 * Build success response
	 * @param string/array $data
	 * @param int  $code
	 * @return Illuminate\Http\JsonResponse
	*/
	public function successResponse($data, $code = Response::HTTP_OK)
	{
		// return response()->json(['data' => $data], $code);
		return response()->json($data, $code);
	}

	// public function authResponse($code = Response::HTTP_OK)
	// {
	// 	$user = User::where('id', Auth::user()->id)->with('subscription')->first();

	// 	if ($user) {
	// 		$user->token = request()->bearerToken();

	// 		return response()->json(['data' => $user], $code);
	// 	}

	// 	return $this->errorResponse('Auth user not found', 422);
	// }


	public function errorResponse($message, $code)
	{
		return response()->json(['error' => $message, 'code' => $code], $code);
	}


	public function verifyPassword($password, $sqlPass){
        if(password_verify($password, $sqlPass)) {
            return true;
        } else {
            return false;
        }
    }


	public function adminAuthError()
	{
		return response()->json(['error' => "Oops! This Admin is unauthorised"], 401);
	}


}
