<?php

declare(strict_types=1);

namespace PetMatch\Application\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Engagement\Favorite;
use PetMatch\Domain\Engagement\FavoriteRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class CreateFavorite
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /** @return array{id: int, user_id: int, pet_id: int} */
    public function execute(int $petId): array
    {
        $userId = $this->sessionManager->userId();
        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $pet = $this->petRepository->findById($petId);
        if ($pet === null || $pet->status !== 'available') {
            throw new FavoritePetUnavailableException('Pet is not available.');
        }

        $id = $this->favoriteRepository->save(new Favorite(null, $userId, $petId));
        return ['id' => $id, 'user_id' => $userId, 'pet_id' => $petId];
    }
}
