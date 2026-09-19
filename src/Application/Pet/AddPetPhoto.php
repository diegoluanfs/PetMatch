<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\Pet\PetPhoto;
use PetMatch\Domain\Pet\PetPhotoRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\Organization\OrganizationRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class AddPetPhoto
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
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(int $petId, array $input): array
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
            throw new ForbiddenException('Only organization users can add pet photos.');
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
            throw new ForbiddenException('Only available pets can receive photos.');
        }

        $path = trim((string) ($input['path'] ?? ''));
        $sortOrder = (int) ($input['sort_order'] ?? 0);

        if ($path === '') {
            throw new InvalidArgumentException('Photo path is required.');
        }

        if ($sortOrder < 0) {
            throw new InvalidArgumentException('Sort order must be a positive integer.');
        }

        $petPhoto = new PetPhoto(null, $pet->id ?? $petId, $path, $sortOrder);
        $id = $this->petPhotoRepository->save($petPhoto);

        return [
            'id' => $id,
            'pet_id' => $petPhoto->petId,
            'path' => $petPhoto->path,
            'sort_order' => $petPhoto->sortOrder,
        ];
    }
}
