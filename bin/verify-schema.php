<?php

declare(strict_types=1);

use PetMatch\Infrastructure\Database\DatabaseConnection;

require dirname(__DIR__) . '/vendor/autoload.php';

$pdo = (new DatabaseConnection())->create();

$migrations = $pdo->query('SELECT migration_name FROM schema_migrations ORDER BY migration_name ASC')
    ->fetchAll(PDO::FETCH_COLUMN);

$tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name ASC")
    ->fetchAll(PDO::FETCH_COLUMN);

echo 'MIGRATIONS=' . implode(',', $migrations) . PHP_EOL;
echo 'TABLES=' . implode(',', $tables) . PHP_EOL;
