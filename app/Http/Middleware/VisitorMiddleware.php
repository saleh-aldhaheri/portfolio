<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class VisitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $uuid = $request->cookie('visitor');

        if (! $uuid) {
            $uuid = (string) Str::uuid();
            $response->cookie('visitor', $uuid, 60 * 24 * 365);
        }

        Visitor::firstOrCreate(
            ['uuid' => $uuid],
            [
                'ip_address' => $request->ip(),
                'city' => $request->header('CF-IPCity') ?? 'Unknown',
                'country' => $request->header('CF-IPCountry') ?? 'Unknown',
                'os' => str_replace('"', '', $request->header('Sec-CH-UA-Platform') ?? 'Unknown'),
                'browser' => $request->header('Sec-CH-UA') ?? $request->userAgent(),
                'language' => substr($request->header('Accept-Language') ?? 'en', 0, 5),
                'referrer' => $request->header('referer'),
            ]
        );

        return $response;
    }
}
