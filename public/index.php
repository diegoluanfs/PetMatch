<?php

declare(strict_types=1);

use PetMatch\Infrastructure\Bootstrap\ApplicationFactory;
use PetMatch\Infrastructure\Http\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::fromGlobals();
$kernel = (new ApplicationFactory())->createKernel();
$response = $kernel->handle($request);

$response->send();
