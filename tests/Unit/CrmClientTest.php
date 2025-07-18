<?php

namespace Tests\Unit;

use App\Services\CrmClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\DTO\SubscriberPayloadDto;

class CrmClientTest extends TestCase
{
    protected string $crmBaseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crmBaseUrl = 'https://devtest-crm-api.com';

        $this->app['config']->set('services.crm_api.base_url', $this->crmBaseUrl);
        $this->app['config']->set('services.crm_api.token',      'fake-token-123');

        Http::fake([
            "{$this->crmBaseUrl}/api/subscriber" => Http::response([
                'subscriber' => [
                    'id'               => '01JZZC49XKHD6MK3B12TB3SF72',
                    'emailAddress'     => 'test@example.com',
                    'firstName'        => 'Test',
                    'lastName'         => 'Smith',
                    'dateOfBirth'      => '2000-01-01',
                    'marketingConsent' => true,
                    'lists'            => [
                        [
                            'id'        => '1',
                            'name'      => 'London',
                            'createdAt' => '2025-07-11T13:39:21+00:00',
                            'updatedAt' => '2025-07-11T13:39:21+00:00',
                        ],
                    ],
                    'createdAt'        => '2025-07-12T13:10:26+00:00',
                    'updatedAt'        => '2025-07-17T13:04:50+00:00',
                ],
            ], 200),
            "{$this->crmBaseUrl}/api/lists"      => Http::response([
                ['id' => '1', 'name' => 'London'],
                ['id' => '2', 'name' => 'Edinburgh'],
                ['id' => '3', 'name' => 'Birmingham'],
            ], 200),
        ]);
    }

    public function test_upsert_subscriber_successful(): void
    {

        $payload = [
            'emailAddress'     => 'test@example.com',
            'firstName'        => 'Test',
            'lastName'         => 'Smith',
            'dateOfBirth'      => '2000-01-01',
            'marketingConsent' => true,
            'subscriptionLists' => ['London'],
        ];
        $dto = SubscriberPayloadDto::fromArray($payload);

        $response = (new CrmClient())->upsertSubscriber($dto);

        Http::assertSent(fn($req) => $req->url() === "{$this->crmBaseUrl}/api/subscriber");

        Http::assertSent(function ($request) use ($payload) {
            return $request->method() === 'PUT'
                && $request->url() === "{$this->crmBaseUrl}/api/subscriber"
                && $request['emailAddress']     === $payload['emailAddress']
                && $request['dateOfBirth']      === $payload['dateOfBirth']
                && $request['marketingConsent'] === $payload['marketingConsent']
                && in_array('1', $request['lists'], true);
        });

        $this->assertTrue($response->successful());
        $this->assertSame(200, $response->status());
        $this->assertSame('01JZZC49XKHD6MK3B12TB3SF72',$response->json('subscriber.id'));
    }

    public function test_get_all_subscriber_lists(): void
    {
        $client = new CrmClient();

        $lists = $client->getLists();


        Http::assertSent(function ($request) {
            return $request->method() === 'GET'
                && $request->url() === "{$this->crmBaseUrl}/api/lists";
        });

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $lists);
        $this->assertNotEmpty($lists);
        $this->assertTrue($lists->contains('name', 'London'));
        $this->assertTrue($lists->contains('name', 'Edinburgh'));
        $this->assertTrue($lists->contains('name', 'Birmingham'));
    }

    //public function test_get_create_subscriber_list(): void {}
}
