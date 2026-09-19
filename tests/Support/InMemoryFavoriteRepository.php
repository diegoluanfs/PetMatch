<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Engagement\Favorite;
use PetMatch\Domain\Engagement\FavoriteRepository;

final class InMemoryFavoriteRepository implements FavoriteRepository
{
    /** @var array<int, Favorite> */
    public array $favorites = [];

    public function save(Favorite $favorite): int
    {
        foreach ($this->favorites as $id => $saved) {
            if ($saved->userId === $favorite->userId && $saved->petId === $favorite->petId) return $id;
        }
        $id = count($this->favorites) + 1;
        $this->favorites[$id] = new Favorite($id, $favorite->userId, $favorite->petId);
        return $id;
    }

    public function delete(int $userId, int $petId): void
    {
        foreach ($this->favorites as $id => $favorite) {
            if ($favorite->userId === $userId && $favorite->petId === $petId) unset($this->favorites[$id]);
        }
    }

    public function findByUserId(int $userId): array
    {
        return array_values(array_map(
            static fn (Favorite $favorite): array => [
                'id' => $favorite->id,
                'pet_id' => $favorite->petId,
                'name' => 'Pet ' . $favorite->petId,
                'description' => 'Pet disponível',
                'animal_type' => 'dog',
                'breed' => 'mixed',
                'city' => 'Santa Maria',
                'state' => 'RS',
            ],
            array_filter($this->favorites, static fn (Favorite $favorite): bool => $favorite->userId === $userId),
        ));
    }
}
