<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Business
{
    use ApiResponder;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role !== 'business') {
            return $this->errorResponse('Oops! sorry you do not have access to this route.', 422);
        }
        return $next($request);
    }
}
