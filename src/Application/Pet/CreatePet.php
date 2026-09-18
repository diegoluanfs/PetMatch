<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\Organization\OrganizationRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class CreatePet
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
        private readonly ?OrganizationRepository $organizationRepository = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(array $input): array
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
            throw new ForbiddenException('Only organization users can create pets.');
        }

        if ($this->organizationRepository !== null) {
            $organization = $this->organizationRepository->findById($currentUser->organizationId);
            if ($organization === null || $organization->status !== 'active') {
                throw new ForbiddenException('The organization must be active to create pets.');
            }
        }

        $name = trim((string) ($input['name'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $animalType = trim((string) ($input['animal_type'] ?? ''));
        $breed = trim((string) ($input['breed'] ?? ''));
        $gender = trim((string) ($input['gender'] ?? 'unknown'));
        $birthDate = trim((string) ($input['birth_date'] ?? ''));
        $size = trim((string) ($input['size'] ?? ''));
        $city = trim((string) ($input['city'] ?? ''));
        $state = strtoupper(trim((string) ($input['state'] ?? '')));
        $latitude = $input['latitude'] ?? null;
        $longitude = $input['longitude'] ?? null;

        if ($name === '' || $description === '' || $animalType === '' || $breed === '' || $size === '' || $city === '' || $state === '') {
            throw new InvalidArgumentException('Required pet fields are missing.');
        }

        if (!in_array($gender, ['male', 'female', 'unknown'], true)) {
            throw new InvalidArgumentException('Invalid gender.');
        }

        if (!in_array($size, ['small', 'medium', 'large', 'extra_large'], true)) {
            throw new InvalidArgumentException('Invalid size.');
        }

        if (mb_strlen($state) !== 2) {
            throw new InvalidArgumentException('State must contain 2 characters.');
        }

        $pet = new Pet(
            null,
            $currentUser->organizationId,
            $name,
            $description,
            $animalType,
            $breed,
            $gender,
            $birthDate === '' ? null : $birthDate,
            $size,
            'available',
            $city,
            $state,
            $latitude === null || $latitude === '' ? null : (string) $latitude,
            $longitude === null || $longitude === '' ? null : (string) $longitude,
        );

        $id = $this->petRepository->save($pet);

        return [
            'id' => $id,
            'organization_id' => $pet->organizationId,
            'name' => $pet->name,
            'description' => $pet->description,
            'animal_type' => $pet->animalType,
            'breed' => $pet->breed,
            'gender' => $pet->gender,
            'birth_date' => $pet->birthDate,
            'size' => $pet->size,
            'status' => $pet->status,
            'city' => $pet->city,
            'state' => $pet->state,
            'latitude' => $pet->latitude,
            'longitude' => $pet->longitude,
        ];
    }
}
