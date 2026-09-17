<?php

declare(strict_types=1);

namespace PetMatch\Application\Pet;

use PetMatch\Domain\Pet\PetRepository;

final class GetPet
{
    public function __construct(
        private readonly PetRepository $petRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $id): array
    {
        $pet = $this->petRepository->findById($id);

        if ($pet === null) {
            throw new PetNotFoundException('Pet not found.');
        }

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
        ];
    }
}
