<?php

namespace App\Logging;

use App\Contracts\RequestResponseLoggerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FileRequestResponseLogger implements RequestResponseLoggerInterface
{
    public function logRequest(Request $request): void
    {
        $data = $request->all();
        Log::info('API Request: ' . $request->method() . ', ' . $request->fullUrl(), [
            'headers' => $request->headers->all(),
            'body'    => $data,
        ]);
    }

    public function logResponse(Response $response, Request $request): void
    {
        Log::info('API Response: ' . $response->getStatusCode() . ', ' . $request->fullUrl(), [
            'headers' => $response->headers->all(),
            'body'    => $response->getContent(),
        ]);
    }
}