<?php

declare(strict_types=1);

namespace PetMatch\Application\Engagement;

use InvalidArgumentException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\Swipe\Swipe;
use PetMatch\Domain\Swipe\SwipeRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class SwipePet
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly SwipeRepository $swipeRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return array{id: int, user_id: int, pet_id: int, action: string}
     */
    public function execute(int $petId, string $action): array
    {
        $userId = $this->sessionManager->userId();

        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        if (!in_array($action, ['like', 'pass'], true)) {
            throw new InvalidArgumentException('Action must be like or pass.');
        }

        $pet = $this->petRepository->findById($petId);

        if ($pet === null || $pet->status !== 'available') {
            throw new InvalidArgumentException('Pet is not available.');
        }

        $swipe = new Swipe(null, $userId, $petId, $action);
        $id = $this->swipeRepository->save($swipe);

        return [
            'id' => $id,
            'user_id' => $userId,
            'pet_id' => $petId,
            'action' => $action,
        ];
    }
}
