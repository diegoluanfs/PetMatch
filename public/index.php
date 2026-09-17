<?php

declare(strict_types=1);

use PetMatch\Infrastructure\Bootstrap\ApplicationFactory;
use PetMatch\Infrastructure\Http\Request;

session_name('petmatch_session');
session_set_cookie_params([
	'lifetime' => 0,
	'path' => '/',
	'secure' => isset($_SERVER['HTTPS']),
	'httponly' => true,
	'samesite' => 'Lax',
]);
session_start();

require dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::fromGlobals();
$kernel = (new ApplicationFactory())->createKernel();
$response = $kernel->handle($request);

$response->send();
