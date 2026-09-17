<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\Pet\PetRepository;

final class PdoPetRepository implements PetRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(int $id): ?Pet
    {
        $statement = $this->pdo->prepare(
            'SELECT id, organization_id, name, description, animal_type, breed, gender, birth_date, size, status, city, state, latitude, longitude
             FROM pets
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, organization_id, name, description, animal_type, breed, gender, birth_date, size, status, city, state, latitude, longitude
             FROM pets
             ORDER BY created_at DESC, id DESC'
        );

        $pets = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $pets[] = $this->hydrate($row);
        }

        return $pets;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Pet
    {
        return new Pet(
            (int) $row['id'],
            (int) $row['organization_id'],
            $row['name'],
            $row['description'],
            $row['animal_type'],
            $row['breed'],
            $row['gender'],
            $row['birth_date'],
            $row['size'],
            $row['status'],
            $row['city'],
            $row['state'],
            $row['latitude'],
            $row['longitude'],
        );
    }
}
