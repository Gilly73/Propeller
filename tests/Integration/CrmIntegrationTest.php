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
        Http::withoutMocking();
        $this->client = new CrmClient();
    }


    public function test_get_lists_from_live_crm(): void
    {
        $lists = $this->client->getLists();

        $this->assertNotEmpty($lists);
        $first = $lists->first();
        $this->assertIsArray($first);
        $this->assertArrayHasKey('id',   $first);
        $this->assertArrayHasKey('name', $first);

        $names = $lists->pluck('name')->all();
        $this->assertContains('London',     $names);
        $this->assertContains('Edinburgh',  $names);
    }


    public function test_upsert_subscriber_against_live_crm(): void
    {
        $lists = $this->client->getLists();
        $firstListId = $lists->first()['id'];

        $dto = new SubscriberPayloadDto(
            emailAddress:     'int-test+'.time().'@example.com',
            firstName:        'Integration',
            lastName:         'Tester',
            dateOfBirth:      new DateTimeImmutable('-20 years'),
            marketingConsent: true,
            lists:            [$firstListId],
        );

        $subscriberId = $this->client->upsertSubscriber($dto);

        $this->assertIsInt($subscriberId);
        $this->assertGreaterThan(0, $subscriberId);
    }
}
