<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\PetPhotoRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\Organization\OrganizationRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class RemovePetPhoto
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly PetPhotoRepository $petPhotoRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
        private readonly ?OrganizationRepository $organizationRepository = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $petId, int $photoId): array
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
            throw new ForbiddenException('Only organization users can remove pet photos.');
        }

        if ($this->organizationRepository !== null) {
            $organization = $this->organizationRepository->findById($currentUser->organizationId);
            if ($organization === null || $organization->status !== 'active') {
                throw new ForbiddenException('The organization must be active to manage pet photos.');
            }
        }

        $pet = $this->petRepository->findById($petId);

        if ($pet === null) {
            throw new PetNotFoundException('Pet not found.');
        }

        if ($pet->organizationId !== $currentUser->organizationId) {
            throw new ForbiddenException('You can only manage your own pets.');
        }

        if ($pet->status !== 'available') {
            throw new ForbiddenException('Only available pets can have photos removed.');
        }

        $photo = $this->petPhotoRepository->findById($photoId);

        if ($photo === null || $photo->petId !== $petId) {
            throw new PetPhotoNotFoundException('Pet photo not found.');
        }

        $this->petPhotoRepository->delete($photoId);

        return [
            'id' => $photoId,
            'pet_id' => $petId,
        ];
    }
}
