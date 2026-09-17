<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ArchivePet
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $petId): array
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
            throw new ForbiddenException('Only organization users can archive pets.');
        }

        $pet = $this->petRepository->findById($petId);

        if ($pet === null) {
            throw new PetNotFoundException('Pet not found.');
        }

        if ($pet->organizationId !== $currentUser->organizationId) {
            throw new ForbiddenException('You can only manage your own pets.');
        }

        $archivedPet = new \PetMatch\Domain\Pet\Pet(
            $pet->id,
            $pet->organizationId,
            $pet->name,
            $pet->description,
            $pet->animalType,
            $pet->breed,
            $pet->gender,
            $pet->birthDate,
            $pet->size,
            'archived',
            $pet->city,
            $pet->state,
            $pet->latitude,
            $pet->longitude,
        );

        $this->petRepository->update($archivedPet);

        return [
            'id' => $archivedPet->id,
            'organization_id' => $archivedPet->organizationId,
            'name' => $archivedPet->name,
            'description' => $archivedPet->description,
            'animal_type' => $archivedPet->animalType,
            'breed' => $archivedPet->breed,
            'gender' => $archivedPet->gender,
            'birth_date' => $archivedPet->birthDate,
            'size' => $archivedPet->size,
            'status' => $archivedPet->status,
            'city' => $archivedPet->city,
            'state' => $archivedPet->state,
            'latitude' => $archivedPet->latitude,
            'longitude' => $archivedPet->longitude,
        ];
    }
}
