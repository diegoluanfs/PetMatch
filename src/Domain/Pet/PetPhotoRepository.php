<?php

declare(strict_types=1);

namespace PetMatch\Domain\Pet;

interface PetPhotoRepository
{
    public function findById(int $id): ?PetPhoto;

    /**
     * @return list<PetPhoto>
     */
    public function findByPetId(int $petId): array;

    public function save(PetPhoto $petPhoto): int;

    public function delete(int $id): void;
}
