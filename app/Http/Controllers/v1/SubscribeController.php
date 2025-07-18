<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CreateSubscriberRequest;
use App\Services\CrmClient;
use App\DTO\SubscriberPayloadDto;
use App\DTO\SubscriberResponseDto;

class SubscribeController extends Controller
{
    public function create(CreateSubscriberRequest $request, CrmClient $crm)
    {
        $dto = SubscriberPayloadDto::fromArray($request->validated());
        $response = $crm->upsertSubscriber($dto);
        return response()->json([
            'subscriber' => SubscriberResponseDto::fromArray($response['subscriber']),
            'message' => 'Subscriber created successfully.',
        ], $response->status());
    }
}
