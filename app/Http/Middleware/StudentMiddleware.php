<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Ensures only authenticated users with the 'student' role can access the route.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Verify the user has the 'student' role
        if (! $request->user()->isStudent()) {
            abort(403, 'Unauthorized access: This portal is reserved for students.');
        }

        return $next($request);
    }
}
