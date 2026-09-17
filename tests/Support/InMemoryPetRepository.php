<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\Pet\PetRepository;

final class InMemoryPetRepository implements PetRepository
{
    /**
     * @var array<int, Pet>
     */
    private array $pets = [];

    public function __construct()
    {
        $this->pets[1] = new Pet(
            1,
            1,
            'Thor',
            'Labrador amigável',
            'dog',
            'labrador',
            'male',
            '2022-01-01',
            'large',
            'available',
            'Santa Maria',
            'RS',
            null,
            null,
        );

        $this->pets[2] = new Pet(
            2,
            1,
            'Luna',
            'Cachorrinha carinhosa',
            'dog',
            'vira-lata',
            'female',
            '2023-02-15',
            'medium',
            'available',
            'Porto Alegre',
            'RS',
            null,
            null,
        );
    }

    public function findById(int $id): ?Pet
    {
        return $this->pets[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->pets);
    }

    public function save(Pet $pet): int
    {
        $id = count($this->pets) + 1;
        $this->pets[$id] = new Pet(
            $id,
            $pet->organizationId,
            $pet->name,
            $pet->description,
            $pet->animalType,
            $pet->breed,
            $pet->gender,
            $pet->birthDate,
            $pet->size,
            $pet->status,
            $pet->city,
            $pet->state,
            $pet->latitude,
            $pet->longitude,
        );

        return $id;
    }
}
