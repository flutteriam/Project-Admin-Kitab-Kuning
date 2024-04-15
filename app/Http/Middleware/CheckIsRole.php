<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // User is not authenticated, redirect them to the login page
            return redirect()->route('login');
        }
        $role_can_access = explode('|', $role);
        // Check if the user has the required role
        if (!$request->user()->hasRole($role_can_access)) {
            // User does not have the required role, redirect them to a route or show an error
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
