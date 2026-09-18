<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Database;

use PDO;
use PetMatch\Application\Transaction\TransactionManager;

final class PdoTransactionManager implements TransactionManager
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
