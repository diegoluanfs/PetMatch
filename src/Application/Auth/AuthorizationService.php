<?php

declare(strict_types=1);

namespace PetMatch\Application\Auth;

final class AuthorizationService
{
    /**
     * @param list<string> $allowedRoles
     */
    public function ensureRole(string $currentRole, array $allowedRoles): void
    {
        if (!in_array($currentRole, $allowedRoles, true)) {
            throw new ForbiddenException('Forbidden.');
        }
    }
}
