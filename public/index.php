<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

header('Content-Type: application/json; charset=utf-8');

if ($path === '/' || $path === '/health') {
	echo json_encode([
		'name' => 'PetMatch',
		'status' => 'ok',
	], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

	return;
}

http_response_code(404);

echo json_encode([
	'error' => 'Not Found',
], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
