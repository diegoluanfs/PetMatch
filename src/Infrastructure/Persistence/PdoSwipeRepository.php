<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Swipe\Swipe;
use PetMatch\Domain\Swipe\SwipeRepository;

final class PdoSwipeRepository implements SwipeRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function save(Swipe $swipe): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO swipes (user_id, pet_id, action)
             VALUES (:user_id, :pet_id, :action)
             ON CONFLICT (user_id, pet_id)
             DO UPDATE SET action = EXCLUDED.action
             RETURNING id'
        );

        $statement->execute([
            'user_id' => $swipe->userId,
            'pet_id' => $swipe->petId,
            'action' => $swipe->action,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function findLikedByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT s.pet_id, p.name, p.description, p.animal_type, p.breed, p.city, p.state
             FROM swipes s
             INNER JOIN pets p ON p.id = s.pet_id
             WHERE s.user_id = :user_id
               AND s.action = \'like\'
               AND p.status = \'available\'
             ORDER BY s.created_at DESC, s.id DESC'
        );

        $statement->execute(['user_id' => $userId]);

        return array_map(
            static fn (array $row): array => [
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
