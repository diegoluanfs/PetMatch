<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Bootstrap;

use PDO;
use PetMatch\Application\System\GetHealthStatus;
use PetMatch\Application\Auth\RegisterUser;
use PetMatch\Infrastructure\Container\Container;
use PetMatch\Infrastructure\Database\DatabaseConnection;
use PetMatch\Infrastructure\Http\ApplicationKernel;
use PetMatch\Infrastructure\Http\Router;
use PetMatch\Infrastructure\Persistence\PdoUserRepository;
use PetMatch\Presentation\Controllers\HealthController;
use PetMatch\Presentation\Controllers\RegisterUserController;

final class ApplicationFactory
{
    public function createKernel(): ApplicationKernel
    {
        $container = new Container();

        $container->set(DatabaseConnection::class, static fn () => new DatabaseConnection());

        $container->set(PDO::class, static fn (Container $container): PDO => $container->get(DatabaseConnection::class)->create());

        $container->set(GetHealthStatus::class, static fn () => new GetHealthStatus());

        $container->set(PdoUserRepository::class, static fn (Container $container): PdoUserRepository => new PdoUserRepository(
            $container->get(PDO::class)
        ));

        $container->set(RegisterUser::class, static fn (Container $container): RegisterUser => new RegisterUser(
            $container->get(PdoUserRepository::class)
        ));

        $container->set(HealthController::class, static fn (Container $container): HealthController => new HealthController(
            $container->get(GetHealthStatus::class)
        ));

        $container->set(RegisterUserController::class, static fn (Container $container): RegisterUserController => new RegisterUserController(
            $container->get(RegisterUser::class)
        ));

        $container->set(Router::class, static fn (Container $container): Router => new Router([
            'GET /health' => $container->get(HealthController::class),
            'GET /' => $container->get(HealthController::class),
            'POST /api/v1/auth/register' => $container->get(RegisterUserController::class),
        ]));

        $container->set(ApplicationKernel::class, static fn (Container $container): ApplicationKernel => new ApplicationKernel(
            $container->get(Router::class)
        ));

        return $container->get(ApplicationKernel::class);
    }
}
