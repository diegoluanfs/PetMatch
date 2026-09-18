<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Persistence;

use PDO;
use PetMatch\Domain\User\UserVerificationRepository;

final class PdoUserVerificationRepository implements UserVerificationRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function isVerified(int $userId, string $type): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT 1
             FROM user_verifications
             WHERE user_id = :user_id
               AND type = :type
               AND status = \'verified\'
             LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'type' => $type,
        ]);

        return $statement->fetchColumn() !== false;
    }
}
