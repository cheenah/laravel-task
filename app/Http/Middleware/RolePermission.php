<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null, $permission = null): Response
    {
        $user = $request->user();

        if ($role && !$user->hasRole($role)) {
            return response()->json(['message' => 'Forbidden: wrong role'], 403);
        }

        if ($permission && !$user->can($permission)) {
            return response()->json(['message' => 'Forbidden: no permission'], 403);
        }

        return $next($request);
    }
}
