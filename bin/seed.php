<?php

declare(strict_types=1);

use PetMatch\Infrastructure\Database\DatabaseConnection;

require dirname(__DIR__) . '/vendor/autoload.php';

$pdo = (new DatabaseConnection())->create();
$pdo->beginTransaction();

try {
    $organizationId = findOrganizationId($pdo, 'ong.admin@example.com');

    if ($organizationId === null) {
        $statement = $pdo->prepare(
            'INSERT INTO organizations (name, description, email, phone, status)
             VALUES (:name, :description, :email, :phone, :status)
             RETURNING id'
        );
        $statement->execute([
            'name' => 'Amigos de Quatro Patas',
            'description' => 'Organização local dedicada ao resgate e adoção responsável.',
            'email' => 'ong.admin@example.com',
            'phone' => '(55) 99999-0000',
            'status' => 'active',
        ]);
        $organizationId = (int) $statement->fetchColumn();
    }

    ensureUser(
        $pdo,
        $organizationId,
        'ONG Admin',
        'ong.admin@example.com',
        'secret123',
        'organization_admin',
        'active',
    );
    ensureVerification($pdo, 'maria@example.com', 'email');
    ensureVerification($pdo, 'maria@example.com', 'whatsapp');
    ensureUser(
        $pdo,
        null,
        'Maria Adotante',
        'maria@example.com',
        'secret123',
        'adopter',
        'active',
    );

    ensurePet($pdo, $organizationId, 'Mimi', 'Gatinha curiosa', 'cat', 'srd', 'female', '2021-05-10', 'small', 'Santa Maria', 'RS');
    ensurePet($pdo, $organizationId, 'Ruiva', 'SRD alegre e brincalhão', 'dog', 'vira-lata', 'female', '2022-03-11', 'medium', 'Santa Maria', 'RS');
    ensurePet($pdo, $organizationId, 'Tico', 'SRD alegre', 'dog', 'vira-lata', 'male', '2022-08-20', 'medium', 'Santa Maria', 'RS');

    $pdo->commit();

    echo "Seed completed.\n";
    echo "Organization: ong.admin@example.com / secret123\n";
    echo "Adopter: maria@example.com / secret123\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    throw $exception;
}

function findOrganizationId(PDO $pdo, string $email): ?int
{
    $statement = $pdo->prepare('SELECT id FROM organizations WHERE lower(email) = lower(:email) LIMIT 1');
    $statement->execute(['email' => $email]);
    $id = $statement->fetchColumn();

    return $id === false ? null : (int) $id;
}

function ensureUser(PDO $pdo, ?int $organizationId, string $name, string $email, string $password, string $role, string $status): void
{
    $statement = $pdo->prepare('SELECT id FROM users WHERE lower(email) = lower(:email) LIMIT 1');
    $statement->execute(['email' => $email]);

    if ($statement->fetchColumn() !== false) {
        return;
    }

    $insert = $pdo->prepare(
        'INSERT INTO users (organization_id, name, email, password_hash, role, status)
         VALUES (:organization_id, :name, :email, :password_hash, :role, :status)'
    );
    $insert->execute([
        'organization_id' => $organizationId,
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
        'status' => $status,
    ]);
}

function ensurePet(PDO $pdo, int $organizationId, string $name, string $description, string $animalType, string $breed, string $gender, string $birthDate, string $size, string $city, string $state): void
{
    $statement = $pdo->prepare(
        'SELECT id FROM pets
         WHERE organization_id = :organization_id AND name = :name
         LIMIT 1'
    );
    $statement->execute([
        'organization_id' => $organizationId,
        'name' => $name,
    ]);

    if ($statement->fetchColumn() !== false) {
        return;
    }

    $insert = $pdo->prepare(
        'INSERT INTO pets (organization_id, name, description, animal_type, breed, gender, birth_date, size, status, city, state)
         VALUES (:organization_id, :name, :description, :animal_type, :breed, :gender, :birth_date, :size, :status, :city, :state)'
    );
    $insert->execute([
        'organization_id' => $organizationId,
        'name' => $name,
        'description' => $description,
        'animal_type' => $animalType,
        'breed' => $breed,
        'gender' => $gender,
        'birth_date' => $birthDate,
        'size' => $size,
        'status' => 'available',
        'city' => $city,
        'state' => $state,
    ]);
}

function ensureVerification(PDO $pdo, string $email, string $type): void
{
    $userStatement = $pdo->prepare('SELECT id FROM users WHERE lower(email) = lower(:email) LIMIT 1');
    $userStatement->execute(['email' => $email]);
    $userId = $userStatement->fetchColumn();

    if ($userId === false) {
        return;
    }

    $statement = $pdo->prepare(
        'SELECT id FROM user_verifications
         WHERE user_id = :user_id AND type = :type AND status = \'verified\'
         LIMIT 1'
    );
    $statement->execute(['user_id' => (int) $userId, 'type' => $type]);

    if ($statement->fetchColumn() !== false) {
        return;
    }

    $insert = $pdo->prepare(
        'INSERT INTO user_verifications (user_id, type, status, verified_at)
         VALUES (:user_id, :type, \'verified\', now())'
    );
    $insert->execute(['user_id' => (int) $userId, 'type' => $type]);
}
