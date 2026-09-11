<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetInterfaceLocale
{
    /**
     * Apply the language selected by either a guest or the signed-in user.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = $request->user()?->interface_language
            ?? $request->cookie('gaspoa_language', 'sw');

        app()->setLocale(in_array($language, ['sw', 'en'], true) ? $language : 'sw');

        return $next($request);
    }
}
