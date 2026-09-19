<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Swipe\Swipe;
use PetMatch\Domain\Swipe\SwipeRepository;

final class InMemorySwipeRepository implements SwipeRepository
{
    /** @var array<int, Swipe> */
    public array $swipes = [];

    public function save(Swipe $swipe): int
    {
        foreach ($this->swipes as $id => $savedSwipe) {
            if ($savedSwipe->userId === $swipe->userId && $savedSwipe->petId === $swipe->petId) {
                $this->swipes[$id] = new Swipe($id, $swipe->userId, $swipe->petId, $swipe->action);
                return $id;
            }
        }

        $id = count($this->swipes) + 1;
        $this->swipes[$id] = new Swipe($id, $swipe->userId, $swipe->petId, $swipe->action);
        return $id;
    }

    public function findLikedByUserId(int $userId): array
    {
        return array_values(array_map(
            static fn (Swipe $swipe): array => [
                'pet_id' => $swipe->petId,
                'name' => 'Pet ' . $swipe->petId,
                'description' => 'Pet disponível',
                'animal_type' => 'dog',
                'breed' => 'mixed',
                'city' => 'Santa Maria',
                'state' => 'RS',
            ],
            array_filter(
                $this->swipes,
                static fn (Swipe $swipe): bool => $swipe->userId === $userId && $swipe->action === 'like',
            ),
        ));
    }

    public function findMatchesByUserId(int $userId): array
    {
        return $this->findLikedByUserId($userId);
    }
}
