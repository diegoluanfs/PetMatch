<?php

declare(strict_types=1);

namespace PetMatch\Application\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Swipe\SwipeRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListMatches
{
    public function __construct(
        private readonly SwipeRepository $swipeRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function execute(): array
    {
        $userId = $this->sessionManager->userId();
        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        return $this->swipeRepository->findMatchesByUserId($userId);
    }
}
