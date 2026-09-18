<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;

final class PdoAdoptionRequestRepository implements AdoptionRequestRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(int $id): ?AdoptionRequest
    {
        $statement = $this->pdo->prepare(
            'SELECT id, user_id, pet_id, status, message
             FROM adoption_requests
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new AdoptionRequest(
            (int) $row['id'],
            (int) $row['user_id'],
            (int) $row['pet_id'],
            $row['status'],
            $row['message'],
        );
    }

    public function findActiveByUserAndPet(int $userId, int $petId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, user_id, pet_id, status, message
             FROM adoption_requests
             WHERE user_id = :user_id
               AND pet_id = :pet_id
               AND status IN (\'pending\', \'approved\')
             LIMIT 1'
        );

        $statement->execute([
            'user_id' => $userId,
            'pet_id' => $petId,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'user_id' => (int) $row['user_id'],
            'pet_id' => (int) $row['pet_id'],
            'status' => $row['status'],
            'message' => $row['message'],
        ];
    }

    public function findByUserId(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, user_id, pet_id, status, message
             FROM adoption_requests
             WHERE user_id = :user_id
             ORDER BY created_at DESC, id DESC'
        );

        $statement->execute(['user_id' => $userId]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $requests = [];

        foreach ($rows as $row) {
            $requests[] = [
                'id' => (int) $row['id'],
                'user_id' => (int) $row['user_id'],
                'pet_id' => (int) $row['pet_id'],
                'status' => $row['status'],
                'message' => $row['message'],
            ];
        }

        return $requests;
    }

    public function findForOrganization(int $organizationId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT ar.id, ar.user_id, ar.pet_id, ar.status, ar.message, p.name AS pet_name
             FROM adoption_requests ar
             INNER JOIN pets p ON p.id = ar.pet_id
             WHERE p.organization_id = :organization_id
             ORDER BY ar.created_at DESC, ar.id DESC'
        );

        $statement->execute(['organization_id' => $organizationId]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $requests = [];

        foreach ($rows as $row) {
            $requests[] = [
                'id' => (int) $row['id'],
                'user_id' => (int) $row['user_id'],
                'pet_id' => (int) $row['pet_id'],
                'pet_name' => $row['pet_name'],
                'status' => $row['status'],
                'message' => $row['message'],
            ];
        }

        return $requests;
    }

    public function save(AdoptionRequest $request): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO adoption_requests (user_id, pet_id, status, message)
             VALUES (:user_id, :pet_id, :status, :message)
             RETURNING id'
        );

        $statement->execute([
            'user_id' => $request->userId,
            'pet_id' => $request->petId,
            'status' => $request->status,
            'message' => $request->message,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function update(AdoptionRequest $request): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE adoption_requests
             SET status = :status,
                 message = :message,
                 updated_at = now()
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $request->id,
            'status' => $request->status,
            'message' => $request->message,
        ]);
    }
}
