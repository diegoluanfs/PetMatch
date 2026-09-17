<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Database;

use PDO;
use RuntimeException;

final class DatabaseConnection
{
    public function create(): PDO
    {
        $dsn = $this->buildDsn();

        $pdo = new PDO(
            $dsn,
            $this->getEnv('DB_USER', 'postgres'),
            $this->getEnv('DB_PASSWORD', ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return $pdo;
    }

    private function buildDsn(): string
    {
        $dsn = $this->getEnv('DB_DSN');

        if ($dsn !== null && $dsn !== '') {
            return $dsn;
        }

        $host = $this->getEnv('DB_HOST', '127.0.0.1');
        $port = $this->getEnv('DB_PORT', '5432');
        $database = $this->getEnv('DB_NAME');

        if ($database === null || $database === '') {
            throw new RuntimeException('Database name is required. Set DB_NAME or DB_DSN.');
        }

        return sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $database);
    }

    private function getEnv(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}
