<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\Organization\Organization;
use PetMatch\Domain\Organization\OrganizationRepository;

final class PdoOrganizationRepository implements OrganizationRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findByEmail(string $email): ?Organization
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, description, email, phone, status
             FROM organizations
             WHERE lower(email) = lower(:email)
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new Organization(
            (int) $row['id'],
            $row['name'],
            $row['description'],
            $row['email'],
            $row['phone'],
            $row['status'],
        );
    }

    public function save(Organization $organization): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO organizations (name, description, email, phone, status)
             VALUES (:name, :description, :email, :phone, :status)
             RETURNING id'
        );

        $statement->execute([
            'name' => $organization->name,
            'description' => $organization->description,
            'email' => $organization->email,
            'phone' => $organization->phone,
            'status' => $organization->status,
        ]);

        return (int) $statement->fetchColumn();
    }
}
