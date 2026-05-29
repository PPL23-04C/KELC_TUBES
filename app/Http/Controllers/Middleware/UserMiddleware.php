<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'user') {
            abort(403);
        }

        return $next($request);
    }
}
