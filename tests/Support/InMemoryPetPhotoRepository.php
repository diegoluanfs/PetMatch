<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Pet\PetPhoto;
use PetMatch\Domain\Pet\PetPhotoRepository;

final class InMemoryPetPhotoRepository implements PetPhotoRepository
{
    /**
     * @var array<int, PetPhoto>
     */
    private array $photos = [];

    public function findById(int $id): ?PetPhoto
    {
        return $this->photos[$id] ?? null;
    }

    public function findByPetId(int $petId): array
    {
        return array_values(array_filter(
            $this->photos,
            static fn (PetPhoto $photo): bool => $photo->petId === $petId
        ));
    }

    public function save(PetPhoto $petPhoto): int
    {
        $id = count($this->photos) + 1;
        $this->photos[$id] = new PetPhoto(
            $id,
            $petPhoto->petId,
            $petPhoto->path,
            $petPhoto->sortOrder,
        );

        return $id;
    }

    public function delete(int $id): void
    {
        unset($this->photos[$id]);
    }
}
