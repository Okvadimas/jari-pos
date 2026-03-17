<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScreenIsNotLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If session is locked, redirect to lock screen
        if (session('locked', false)) {
            // Allow access to logout to prevent being stuck forever. Check if route name is logout.
            if ($request->route() && $request->route()->getName() === 'logout') {
                return $next($request);
            }

            // check if request is ajax
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Screen is locked.'
                ], 403);
            }

            return redirect()->route('lock-screen');
        }

        return $next($request);
    }
}
