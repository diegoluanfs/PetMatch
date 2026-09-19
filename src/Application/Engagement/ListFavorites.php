<?php

declare(strict_types=1);

namespace PetMatch\Application\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Engagement\FavoriteRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListFavorites
{
    public function __construct(
        private readonly FavoriteRepository $favoriteRepository,
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

        return $this->favoriteRepository->findByUserId($userId);
    }
}
