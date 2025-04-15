<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Default to Spanish if no header is provided
        $lang = $request->header('Accept-Language', 'es'); 

        // Validate supported languages
        if (!in_array($lang, ['en', 'es'])) {
            $lang = 'es';
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
