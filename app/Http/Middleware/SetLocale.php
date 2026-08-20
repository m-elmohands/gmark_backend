<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Accept-Language', config('app.fallback_locale', 'en'));
        $preferred = trim(explode(',', $header)[0]);
        $locale = strtolower(substr(str_replace('_', '-', $preferred), 0, 2));

        abort_unless(in_array($locale, config('translatable.locales'), true), 406, 'Unsupported language. Use Accept-Language: en or ar.');
        app()->setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }
}
