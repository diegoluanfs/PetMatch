<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use PetMatch\Domain\Pet\PetPhotoRepository;
use PetMatch\Domain\Pet\PetRepository;

final class ListPets
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly PetPhotoRepository $petPhotoRepository,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function execute(): array
    {
        return array_map(
            function ($pet): array {
                $photos = array_map(
                    static fn ($photo): array => [
                        'id' => $photo->id,
                        'pet_id' => $photo->petId,
                        'path' => $photo->path,
                        'sort_order' => $photo->sortOrder,
                    ],
                    $this->petPhotoRepository->findByPetId($pet->id ?? 0)
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
            $this->petRepository->findAll()
        );
    }
}
