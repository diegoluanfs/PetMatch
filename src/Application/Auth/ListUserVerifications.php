<?php

declare(strict_types=1);

namespace PetMatch\Application\Auth;

use PetMatch\Domain\User\UserVerificationRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListUserVerifications
{
    public function __construct(
        private readonly UserVerificationRepository $verificationRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(): array
    {
        $userId = $this->sessionManager->userId();

        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        return $this->verificationRepository->findByUserId($userId);
    }
}
