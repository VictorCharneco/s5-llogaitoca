<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated. Please log in to access this resource.'
            ], 401);
        }

        if($user->role === 'admin'){
            return response()->json([
                'message' => 'Forbidden. This action is not allowed for administrators.'
            ], 403);
        }

        return $next($request);
    }
}
