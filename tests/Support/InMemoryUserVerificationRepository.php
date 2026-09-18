<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\User\UserVerificationRepository;

final class InMemoryUserVerificationRepository implements UserVerificationRepository
{
    /** @var array<string, bool> */
    private array $verified = [];

    /** @var array<int, list<array{type: string, status: string, verified_at: string|null}>> */
    private array $records = [];

    public function verify(int $userId, string $type): void
    {
        $this->verified[$userId . ':' . $type] = true;
        $this->records[$userId][] = [
            'type' => $type,
            'status' => 'verified',
            'verified_at' => '2026-09-18T00:00:00+00:00',
        ];
    }

    public function isVerified(int $userId, string $type): bool
    {
        return $this->verified[$userId . ':' . $type] ?? false;
    }

    public function findByUserId(int $userId): array
    {
        return $this->records[$userId] ?? [];
    }
}
