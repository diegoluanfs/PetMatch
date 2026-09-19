<?php

declare(strict_types=1);

namespace PetMatch\Domain\Swipe;

interface SwipeRepository
{
    public function save(Swipe $swipe): int;

    /**
     * @return list<array<string, mixed>>
     */
    public function findLikedByUserId(int $userId): array;

    /**
     * @return list<array<string, mixed>>
     */
    public function findMatchesByUserId(int $userId): array;
}
