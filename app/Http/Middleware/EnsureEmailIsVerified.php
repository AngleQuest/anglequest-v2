<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    use ApiResponder;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->email_verified_at) {
            return $this->errorResponse('Oops! you are yet to verify your email.', 422);
        }
        return $next($request);
    }
}
