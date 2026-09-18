<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\User\UserVerificationRepository;

final class InMemoryUserVerificationRepository implements UserVerificationRepository
{
    /** @var array<string, bool> */
    private array $verified = [];

    public function verify(int $userId, string $type): void
    {
        $this->verified[$userId . ':' . $type] = true;
    }

    public function isVerified(int $userId, string $type): bool
    {
        return $this->verified[$userId . ':' . $type] ?? false;
    }
}
