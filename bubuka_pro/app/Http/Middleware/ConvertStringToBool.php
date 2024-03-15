<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertStringToBool
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        foreach ($request->all() as $key => $value) {
            if ($value === 'true' || $value === true) {
                $request[$key] = 1;
            }
            if ($value === 'false' || $value === false) {
                $request[$key] = 0;
            }
        }

        return $next($request);
    }
}
