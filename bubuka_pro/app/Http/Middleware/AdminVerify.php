<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $user = User::find(Auth::id());

        if (!$user || !$user->is_admin) {

            throw new Exception('Forbidden', 403);
        }

        return $next($request);
    }
}
