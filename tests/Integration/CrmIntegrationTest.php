<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\CrmClient;
use App\DTO\SubscriberPayloadDto;
use DateTimeImmutable;

class CrmIntegrationTest extends TestCase
{
    /** @var CrmClient */
    private CrmClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new CrmClient();
    }


    public function test_get_lists_from_live_crm(): void
    {
        $lists = $this->client->getLists();

        $this->assertNotEmpty($lists);
        $data = $lists->first();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id',   $data[0]);
        $this->assertArrayHasKey('name', $data[0]);

        $names = collect($data)->pluck('name')->all();

        $this->assertContains('London',     $names);
        $this->assertContains('Edinburgh',  $names);
    }


    public function test_upsert_subscriber_against_live_crm(): void
    {

        $lists = $this->client->getLists();
        $this->assertNotEmpty($lists->get('lists'));
        $allLists = $lists->get('lists');

        $listToUse = $allLists[1] ?? $allLists[0];

        $this->assertArrayHasKey('name', $listToUse);

        $dto = SubscriberPayloadDto::fromArray([
            'emailAddress'      => 'int-test+' . time() . '@example.com',
            'firstName'         => 'Integration',
            'lastName'          => 'Tester',
            'dateOfBirth'       => now()->subYears(20)->toDateString(),
            'marketingConsent'  => true,
            'subscriptionLists' => [$listToUse['name']],
        ]);


        $response = $this->client->upsertSubscriber($dto);
        $this->assertInstanceOf(\Illuminate\Http\Client\Response::class, $response);
        $this->assertTrue($response->successful());

        $data = $response->json();

        $this->assertArrayHasKey('subscriber', $data);
        $this->assertIsArray($data['subscriber']);

        $this->assertArrayHasKey('id', $data['subscriber']);
        $this->assertNotEmpty($data['subscriber']['id']);
    }
}
