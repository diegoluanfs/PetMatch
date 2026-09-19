<?php

declare(strict_types=1);

namespace PetMatch\Domain\Engagement;

interface FavoriteRepository
{
    public function save(Favorite $favorite): int;

    public function delete(int $userId, int $petId): void;

    /**
     * @return list<array<string, mixed>>
     */
    public function findByUserId(int $userId): array;
}
