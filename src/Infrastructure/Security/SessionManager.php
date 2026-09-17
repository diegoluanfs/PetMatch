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

    public function userRole(): ?string
    {
        $role = $_SESSION['user_role'] ?? null;

        if (!is_string($role) || $role === '') {
            return null;
        }

        return $role;
    }

    public function clear(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
    }
}
