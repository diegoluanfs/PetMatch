<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListAdoptionRequests
{
    public function __construct(
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(): array
    {
        $currentUserId = $this->sessionManager->userId();

        if ($currentUserId === null) {
            return [];
        }

        return $this->adoptionRequestRepository->findByUserId($currentUserId);
    }
}
