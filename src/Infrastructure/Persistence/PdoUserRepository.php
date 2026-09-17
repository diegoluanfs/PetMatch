<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\User\User;
use PetMatch\Domain\User\UserRepository;

final class PdoUserRepository implements UserRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, role, status
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['name'],
            $row['email'],
            $row['password_hash'],
            $row['role'],
            $row['status'],
        );
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, role, status
             FROM users
             WHERE lower(email) = lower(:email)
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['name'],
            $row['email'],
            $row['password_hash'],
            $row['role'],
            $row['status'],
        );
    }

    public function save(User $user): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role, status)
             VALUES (:name, :email, :password_hash, :role, :status)
             RETURNING id'
        );

        $statement->execute([
            'name' => $user->name,
            'email' => $user->email,
            'password_hash' => $user->passwordHash,
            'role' => $user->role,
            'status' => $user->status,
        ]);

        return (int) $statement->fetchColumn();
    }
}
