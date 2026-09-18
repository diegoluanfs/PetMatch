<?php

declare(strict_types=1);

namespace PetMatch\Domain\User;

interface UserVerificationRepository
{
    public function isVerified(int $userId, string $type): bool;

    /**
     * @return list<array{type: string, status: string, verified_at: string|null}>
     */
    public function findByUserId(int $userId): array;
}
