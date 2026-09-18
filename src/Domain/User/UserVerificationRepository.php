<?php

declare(strict_types=1);

namespace PetMatch\Domain\User;

interface UserVerificationRepository
{
    public function isVerified(int $userId, string $type): bool;
}
