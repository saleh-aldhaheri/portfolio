<?php

namespace App\Services;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class VisitorService
{
    /**
     * Resolve the visitor for the request, creating one when no valid
     * visitor cookie is present.
     */
    public function resolve(Request $request): Visitor
    {
        if ($visitor = $request->attributes->get('_resolved_visitor')) {
            return $visitor;
        }

        $uuid = $request->cookie('visitor');

        if ($uuid && $visitor = Visitor::where('uuid', $uuid)->first()) {
            $visitor->update([
                'ip_address' => $request->header('CF-Connecting-IP') ?? $request->ip(),
            ]);
        } else {
            $visitor = Visitor::create([
                'uuid' => $uuid ?? (string) Str::uuid(),
                'ip_address' => $request->header('CF-Connecting-IP') ?? $request->ip(),
                'city' => $request->header('CF-IPCity') ?? 'Unknown',
                'country' => $request->header('CF-IPCountry') ?? 'Unknown',
                'os' => str_replace('"', '', $request->header('Sec-CH-UA-Platform') ?? 'Unknown'),
                'browser' => $request->header('Sec-CH-UA') ?? $request->userAgent(),
            ]);

            Cookie::queue('visitor', $visitor->uuid, 60 * 24 * 365);
        }

        $request->attributes->set('_resolved_visitor', $visitor);

        return $visitor;
    }
}
