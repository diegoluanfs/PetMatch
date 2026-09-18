<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListOrganizationAdoptionRequests
{
    public function __construct(
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly UserRepository $userRepository,
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
            throw new NotAuthenticatedException('Authentication required.');
        }

        $currentUser = $this->userRepository->findById($currentUserId);

        if ($currentUser === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        if (!in_array($currentUser->role, ['organization_admin', 'admin'], true) || $currentUser->organizationId === null) {
            throw new ForbiddenException('Only organization users can list adoption requests.');
        }

        return $this->adoptionRequestRepository->findForOrganization($currentUser->organizationId);
    }
}
