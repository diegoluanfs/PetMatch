<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Engagement\Favorite;
use PetMatch\Domain\Engagement\FavoriteRepository;

final class PdoFavoriteRepository implements FavoriteRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(Favorite $favorite): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO favorites (user_id, pet_id)
             VALUES (:user_id, :pet_id)
             ON CONFLICT (user_id, pet_id) DO UPDATE SET pet_id = EXCLUDED.pet_id
             RETURNING id'
        );
        $statement->execute(['user_id' => $favorite->userId, 'pet_id' => $favorite->petId]);

        return (int) $statement->fetchColumn();
    }

    public function delete(int $userId, int $petId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM favorites WHERE user_id = :user_id AND pet_id = :pet_id');
        $statement->execute(['user_id' => $userId, 'pet_id' => $petId]);
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT f.id, f.pet_id, p.name, p.description, p.animal_type, p.breed, p.city, p.state
             FROM favorites f
             INNER JOIN pets p ON p.id = f.pet_id
             WHERE f.user_id = :user_id AND p.status = \'available\'
             ORDER BY f.created_at DESC, f.id DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return array_map(
            static fn (array $row): array => [
                'id' => (int) $row['id'],
                'pet_id' => (int) $row['pet_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'animal_type' => $row['animal_type'],
                'breed' => $row['breed'],
                'city' => $row['city'],
                'state' => $row['state'],
            ],
            $statement->fetchAll(PDO::FETCH_ASSOC),
        );
    }
}
