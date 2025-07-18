<?php

namespace App\DTO;

use DateTimeImmutable;

readonly class SubscriberPayloadDto
{
    public function __construct(
        public string $emailAddress,
        public string $firstName,
        public string $lastName,
        public DateTimeImmutable $dateOfBirth,
        public bool $marketingConsent,
        /** @param string[]|null $subscriptionLists */
        public array $subscriptionLists,
        /** @param int[]|null $listIds */
        public ?array $lists = null,
    ) {}

    /**
     * Create from a raw array (e.g. request->validated()).
     *
     * @param  array<string,mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['emailAddress'],
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            new DateTimeImmutable($data['dateOfBirth']),
            (bool) $data['marketingConsent'] ?? false,
            $data['subscriptionLists'] ?? null,
            $data['lists'] ?? null,

        );
    }

    public function toArray(): array
    {
        $result = [
            'emailAddress'     => $this->emailAddress,
            'firstName'        => $this->firstName,
            'lastName'         => $this->lastName,
            'dateOfBirth'      => $this->dateOfBirth->format('Y-m-d'),
            'marketingConsent' => $this->marketingConsent,
            'subscriptionLists' => $this->subscriptionLists,
            'lists'            => $this->lists,

        ];
        if ($this->subscriptionLists !== null) {
            $result['lists'] = $this->lists;
        }

        return $result;
    }

    public function withListIds(array $listIds): self
    {
        return new self(
            emailAddress: $this->emailAddress,
            firstName: $this->firstName,
            lastName: $this->lastName,
            dateOfBirth: $this->dateOfBirth,
            marketingConsent: $this->marketingConsent,
            subscriptionLists: $this->subscriptionLists,
            lists: $listIds,
        );
    }
}
