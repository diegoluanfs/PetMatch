<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Security;

final class SessionManager
{
    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public function setUserId(int $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    public function setUserRole(string $role): void
    {
        $_SESSION['user_role'] = $role;
    }

    public function userId(): ?int
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!is_int($userId)) {
            return null;
        }

        return $userId;
    }
}
