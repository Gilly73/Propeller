<?php

namespace App\DTO;

readonly class SubscriberResponseDto
{
    public function __construct(
        public string $id,
        public string $emailAddress,
        public string $firstName,
        public string $lastName,
        public ?string $dateOfBirth,
        public bool $marketingConsent,
        public array $lists,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['emailAddress'],
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            $data['dateOfBirth'] ?? null,
            $data['marketingConsent'],
            $data['lists'] ?? [],
            $data['createdAt'],
            $data['updatedAt'],
        );
    }
}
