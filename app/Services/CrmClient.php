<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\DTO\SubscriberPayloadDto;
use Illuminate\Support\Collection;

class CrmClient
{
    private readonly string $baseUrl;
    private readonly string $token;

    private const DEFAULT_SUBSCRIPTION_LISTS = [
        'London',
        'Birmingham',
        'Edinburgh',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.crm_api.base_url');
        $this->token   = config('services.crm_api.token');
    }

    public function upsertSubscriber(SubscriberPayloadDto $payloadDto): Response|Exception|null
    {
        try {

            $payloadDto = $this->addToDefaultSubcriberLists($payloadDto);

            $payload = $payloadDto->toArray();

            $response = Http::withToken($this->token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put("{$this->baseUrl}/api/subscriber", $payload);

            if ($response->failed()) {
                throw new \Exception('Failed to upsert subscriber: ' . $response->body());
            }

            if ($response->successful()) {
                return $response;
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to upsert subscriber: ' . $response->body());
        }
        return null;
    }

    private function addToDefaultSubcriberLists($payloadDto): SubscriberPayloadDto
    {
        if (! $payloadDto->marketingConsent) {
            return $payloadDto;
        }

        $lists = $this->getLists();
        $lists = collect($lists->get('lists'));

        $listIds = $lists->whereIn('name', $payloadDto->subscriptionLists)->pluck('id')->toArray();

        if (empty($listIds)) {
            $listIds = $lists
                ->whereIn('name',  self::DEFAULT_SUBSCRIPTION_LISTS)
                ->pluck('id')
                ->toArray();
        }

        return $payloadDto->withListIds($listIds);

    }

    public function getLists(): Collection
    {
        try {
            $response = Http::withToken($this->token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get("{$this->baseUrl}/api/lists");

            if ($response->failed()) {
                throw new \Exception('Failed to fetch lists: ' . $response->body());
            }

            return collect($response->json());
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch lists: ' . $e->getMessage());
        }
    }
}
