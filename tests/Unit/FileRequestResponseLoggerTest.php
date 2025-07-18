<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Logging\FileRequestResponseLogger;

class FileRequestResponseLoggerTest extends TestCase
{
    public function test_log_request_records_info(): void
    {
        // Spy on the Log facade
        Log::spy();

        $request = Request::create('/api/test', 'POST', ['email' => 'test@example.com']);
        $request->headers->set('X-Custom-Header', 'HeaderValue');


        $logger = new FileRequestResponseLogger();
        $logger->logRequest($request);

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function ($message, $context) use ($request) {
                // Message should contain method and URL
                $hasMessage = strpos($message, 'API Request: POST, ' . $request->fullUrl()) !== false;

                // Context should include headers and body
                $hasHeader = isset($context['headers']['x-custom-header'])
                             && $context['headers']['x-custom-header'][0] === 'HeaderValue';
                $hasBody   = isset($context['body']['email'])
                             && $context['body']['email'] === 'test@example.com';

                return $hasMessage && $hasHeader && $hasBody;
            });
    }

    public function test_log_response_records_info(): void
    {

        Log::spy();

        $request  = Request::create('/api/test', 'GET');
        $response = new Response('{"status":"ok"}', 201, ['Content-Type' => 'application/json']);

        $logger = new FileRequestResponseLogger();
        $logger->logResponse($response, $request);


        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(function ($message, $context) use ($request, $response) {
                // Message should contain status code and URL
                $hasMessage = strpos($message, 'API Response: 201, ' . $request->fullUrl()) !== false;

                // Context should include headers and body
                $hasHeader = isset($context['headers']['content-type'])
                             && $context['headers']['content-type'][0] === 'application/json';
                $hasBody   = $context['body'] === $response->getContent();

                return $hasMessage && $hasHeader && $hasBody;
            });
    }
}
