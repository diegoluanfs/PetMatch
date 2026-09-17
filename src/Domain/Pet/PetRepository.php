<?php

declare(strict_types=1);

namespace PetMatch\Domain\Pet;

interface PetRepository
{
    public function findById(int $id): ?Pet;

    /**
     * @return list<Pet>
     */
    public function findAll(): array;

    public function save(Pet $pet): int;

    public function update(Pet $pet): void;
}
