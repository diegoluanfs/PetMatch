<?php

declare(strict_types=1);

namespace PetMatch\Application\Auth;

use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class GetAuthenticatedUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return array{id: int, name: string, email: string, role: string, status: string}
     */
    public function execute(): array
    {
        $userId = $this->sessionManager->userId();

        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        return [
            'id' => $user->id ?? 0,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }
}
