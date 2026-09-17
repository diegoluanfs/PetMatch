<?php

declare(strict_types=1);

use PetMatch\Infrastructure\Database\DatabaseConnection;
use PetMatch\Infrastructure\Database\MigrationExecutor;

require dirname(__DIR__) . '/vendor/autoload.php';

$connection = new DatabaseConnection();
$pdo = $connection->create();

$executor = new MigrationExecutor(
    $pdo,
    dirname(__DIR__) . '/database/migrations'
);

$appliedMigrations = $executor->migrate();

if ($appliedMigrations === []) {
    echo "No pending migrations.\n";
    exit(0);
}

echo "Applied migrations:\n";

foreach ($appliedMigrations as $migration) {
    echo sprintf('- %s\n', $migration);
}
