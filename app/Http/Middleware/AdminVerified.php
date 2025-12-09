<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminVerified
{
    public function handle($request, Closure $next)
    {
        if (!session('admin_verified')) {
            return redirect('/')->with('error', 'Admin access required.');
        }

        return $next($request);
    }
}
