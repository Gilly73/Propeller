<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestResponseLoggerInterface
{
    public function logRequest(Request $request): void;
    public function logResponse(Response $response, Request $request): void;
}
