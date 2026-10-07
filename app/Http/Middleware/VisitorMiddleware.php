<?php

namespace App\Http\Middleware;

use App\Services\VisitorService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class VisitorMiddleware
{
    public function __construct(private VisitorService $visitorService) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $this->visitorService->resolve($request);

        return $response;
    }
}
