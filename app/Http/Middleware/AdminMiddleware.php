<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->is_admin) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Non autorisé.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être administrateur.');
    }
}
