<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Pet\PetPhoto;
use PetMatch\Domain\Pet\PetPhotoRepository;

final class PdoPetPhotoRepository implements PetPhotoRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(int $id): ?PetPhoto
    {
        $statement = $this->pdo->prepare(
            'SELECT id, pet_id, path, sort_order
             FROM pet_photos
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

    public function findByPetId(int $petId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, pet_id, path, sort_order
             FROM pet_photos
             WHERE pet_id = :pet_id
             ORDER BY sort_order ASC, created_at ASC, id ASC'
        );
        $statement->execute(['pet_id' => $petId]);

        $photos = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $photos[] = $this->hydrate($row);
        }

        return $photos;
    }

    public function save(PetPhoto $petPhoto): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO pet_photos (pet_id, path, sort_order)
             VALUES (:pet_id, :path, :sort_order)
             RETURNING id'
        );

        $statement->execute([
            'pet_id' => $petPhoto->petId,
            'path' => $petPhoto->path,
            'sort_order' => $petPhoto->sortOrder,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM pet_photos WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): PetPhoto
    {
        return new PetPhoto(
            (int) $row['id'],
            (int) $row['pet_id'],
            $row['path'],
            (int) $row['sort_order'],
        );
    }
}
