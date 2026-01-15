<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menuCode  Menu code to check (e.g., 'MJ-01')
     */
    public function handle(Request $request, Closure $next, string $menuCode): Response
    {
        if (!Gate::allows('access-menu', $menuCode)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Anda tidak memiliki akses ke menu ini',
                    'data'    => [],
                ], 403);
            }

            abort(403, 'Anda tidak memiliki akses ke menu ini');
        }

        return $next($request);
    }
}
