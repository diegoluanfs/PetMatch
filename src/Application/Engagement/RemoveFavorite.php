<?php

declare(strict_types=1);

namespace PetMatch\Application\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Engagement\FavoriteRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class RemoveFavorite
{
    public function __construct(
        private readonly FavoriteRepository $favoriteRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /** @return array{user_id: int, pet_id: int} */
    public function execute(int $petId): array
    {
        $userId = $this->sessionManager->userId();
        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $this->favoriteRepository->delete($userId, $petId);
        return ['user_id' => $userId, 'pet_id' => $petId];
    }
}
