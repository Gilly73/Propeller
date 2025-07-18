<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use App\Services\CrmClient;
use App\DTO\SubscriberPayloadDto;

class SubscriberControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_subscriber_controller_can_be_created_with_valid_data(): void
    {

        $payload = [
            'emailAddress'     => $this->faker->safeEmail,
            'firstName'        => $this->faker->firstName,
            'lastName'         => $this->faker->lastName,
            'dateOfBirth'     => now()->subYears(20)->toDateString(),
            'marketingConsent' => true,
            'subscriptionlists' => ['London'],
        ];

        $crmMock = Mockery::mock(CrmClient::class);
        $this->app->instance(CrmClient::class, $crmMock);

        $crmMock
            ->shouldReceive('getLists')
            ->once()
            ->andReturn(collect([
                ['id' => 10, 'name' => 'London'],
                ['id' => 20, 'name' => 'Edinburgh'],
            ]));

        $crmMock
            ->shouldReceive('upsertSubscriber')
            ->once()
            ->with(Mockery::on(function (SubscriberPayloadDto $dto) use ($payload) {
                return
                    $dto->emailAddress     === $payload['emailAddress'] &&
                    $dto->firstName        === $payload['firstName'] &&
                    $dto->lastName         === $payload['lastName'] &&
                    $dto->dateOfBirth->format('Y-m-d') === $payload['dateOfBirth'] &&
                    $dto->marketingConsent === $payload['marketingConsent'] &&
                    $dto->lists === [10, 20];
            }))
            ->andReturn([
                'subscriber' => [
                    'id'               => '01JZZC49XKHD6MK3B12TB3SF72',
                    'emailAddress'     => $payload['emailAddress'],
                    'firstName'        => $payload['firstName'],
                    'lastName'         => $payload['lastName'],
                    'dateOfBirth'      => $payload['dateOfBirth'],
                    'marketingConsent' => $payload['marketingConsent'],
                    'lists'            => [
                        ['id' => '01JZWVCH8EQS10ZWHYXKM106X4', 'name' => 'London'],
                    ],
                    'createdAt'        => now()->toIso8601String(),
                    'updatedAt'        => now()->toIso8601String(),
                ],
            ]);


        $response = $this->postJson('/api/v1/subscriber/create', $payload);

        $response
            ->assertStatus(201)
            ->assertExactJson(['subscriber_id' => '555']);
    }

    //public function test_subscriber_rejected_if_under_18(): void {}

    //public function test_valid_subscriber_can_be_linked_with_enquiry(): void {}

    //public function test_valid_subscriber_no_consent_can_be_linked_with_enquiry(): void {}
}
