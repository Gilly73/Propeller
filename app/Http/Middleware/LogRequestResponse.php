<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\RequestResponseLoggerInterface;

class LogRequestResponse
{
    public function __construct(
        protected RequestResponseLoggerInterface $logger
    ) {}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Log the incoming request
        $this->logger->logRequest($request);

        // Process the request
        $response = $next($request);

        // Log the outgoing response
        $this->logger->logResponse($response, $request);

        return $response;
    }
}
