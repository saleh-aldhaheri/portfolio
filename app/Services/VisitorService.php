<?php

namespace App\Services;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorService
{
    /**
     * Resolve the visitor for the request, creating one when no valid
     * visitor cookie is present.
     */
    public function resolve(Request $request): Visitor
    {
        $uuid = $request->cookie('visitor');

        if ($uuid && $visitor = Visitor::where('uuid', $uuid)->first()) {
            return $visitor;
        }

        return Visitor::create([
            'uuid' => $uuid ?? (string) Str::uuid(),
            'ip_address' => $request->ip(),
            'city' => $request->header('CF-IPCity') ?? 'Unknown',
            'country' => $request->header('CF-IPCountry') ?? 'Unknown',
            'os' => str_replace('"', '', $request->header('Sec-CH-UA-Platform') ?? 'Unknown'),
            'browser' => $request->header('Sec-CH-UA') ?? $request->userAgent(),
            'language' => substr($request->header('Accept-Language') ?? 'en', 0, 5),
            'referrer' => $request->header('referer'),
        ]);
    }
}
