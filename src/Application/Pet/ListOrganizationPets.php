<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\PetPhotoRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ListOrganizationPets
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly PetPhotoRepository $petPhotoRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(): array
    {
        $userId = $this->sessionManager->userId();

        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        if (!in_array($user->role, ['organization_admin', 'admin'], true) || $user->organizationId === null) {
            throw new ForbiddenException('Only organization users can list organization pets.');
        }

        return array_map(
            function ($pet): array {
                $photos = array_map(
                    static fn ($photo): array => [
                        'id' => $photo->id,
                        'pet_id' => $photo->petId,
                        'path' => $photo->path,
                        'sort_order' => $photo->sortOrder,
                    ],
                    $this->petPhotoRepository->findByPetId($pet->id ?? 0),
                );

                return [
                    'id' => $pet->id,
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
                    'photos' => $photos,
                ];
            },
            $this->petRepository->findAllByOrganizationId($user->organizationId),
        );
    }
}
