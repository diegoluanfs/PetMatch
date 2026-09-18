<?php

declare(strict_types=1);

namespace PetMatch\Application\Transaction;

interface TransactionManager
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;
}
