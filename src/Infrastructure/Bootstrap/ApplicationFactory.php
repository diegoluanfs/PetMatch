<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Bootstrap;

use PDO;
use PetMatch\Application\System\GetHealthStatus;
use PetMatch\Application\Auth\RegisterUser;
use PetMatch\Application\Auth\GetAuthenticatedUser;
use PetMatch\Application\Auth\AuthorizationService;
use PetMatch\Application\Auth\LoginUser;
use PetMatch\Application\Organization\CreateOrganization;
use PetMatch\Application\Pet\GetPet;
use PetMatch\Application\Pet\CreatePet;
use PetMatch\Application\Pet\ArchivePet;
use PetMatch\Application\Pet\AddPetPhoto;
use PetMatch\Application\Pet\RemovePetPhoto;
use PetMatch\Application\Pet\ListPets;
use PetMatch\Application\Pet\UpdatePet;
use PetMatch\Infrastructure\Container\Container;
use PetMatch\Infrastructure\Database\DatabaseConnection;
use PetMatch\Infrastructure\Http\ApplicationKernel;
use PetMatch\Infrastructure\Http\Router;
use PetMatch\Infrastructure\Persistence\PdoPetRepository;
use PetMatch\Infrastructure\Persistence\PdoPetPhotoRepository;
use PetMatch\Infrastructure\Persistence\PdoOrganizationRepository;
use PetMatch\Infrastructure\Persistence\PdoUserRepository;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Presentation\Controllers\HealthController;
use PetMatch\Presentation\Controllers\AuthenticatedUserController;
use PetMatch\Presentation\Controllers\GetPetController;
use PetMatch\Presentation\Controllers\CreatePetController;
use PetMatch\Presentation\Controllers\ArchivePetController;
use PetMatch\Presentation\Controllers\AddPetPhotoController;
use PetMatch\Presentation\Controllers\RemovePetPhotoController;
use PetMatch\Presentation\Controllers\UpdatePetController;
use PetMatch\Presentation\Controllers\LoginUserController;
use PetMatch\Presentation\Controllers\LogoutUserController;
use PetMatch\Presentation\Controllers\ListPetsController;
use PetMatch\Presentation\Controllers\RegisterUserController;
use PetMatch\Presentation\Controllers\CreateOrganizationController;

final class ApplicationFactory
{
    public function createKernel(): ApplicationKernel
    {
        $container = new Container();

        $container->set(DatabaseConnection::class, static fn () => new DatabaseConnection());

        $container->set(PDO::class, static fn (Container $container): PDO => $container->get(DatabaseConnection::class)->create());

        $container->set(SessionManager::class, static fn () => new SessionManager());

        $container->set(GetHealthStatus::class, static fn () => new GetHealthStatus());

        $container->set(PdoUserRepository::class, static fn (Container $container): PdoUserRepository => new PdoUserRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoOrganizationRepository::class, static fn (Container $container): PdoOrganizationRepository => new PdoOrganizationRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoPetRepository::class, static fn (Container $container): PdoPetRepository => new PdoPetRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoPetPhotoRepository::class, static fn (Container $container): PdoPetPhotoRepository => new PdoPetPhotoRepository(
            $container->get(PDO::class)
        ));

        $container->set(RegisterUser::class, static fn (Container $container): RegisterUser => new RegisterUser(
            $container->get(PdoUserRepository::class)
        ));

        $container->set(CreateOrganization::class, static fn (Container $container): CreateOrganization => new CreateOrganization(
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(ListPets::class, static fn (Container $container): ListPets => new ListPets(
            $container->get(PdoPetRepository::class)
        ));

        $container->set(GetPet::class, static fn (Container $container): GetPet => new GetPet(
            $container->get(PdoPetRepository::class)
        ));

        $container->set(CreatePet::class, static fn (Container $container): CreatePet => new CreatePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(ArchivePet::class, static fn (Container $container): ArchivePet => new ArchivePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(AddPetPhoto::class, static fn (Container $container): AddPetPhoto => new AddPetPhoto(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(RemovePetPhoto::class, static fn (Container $container): RemovePetPhoto => new RemovePetPhoto(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(UpdatePet::class, static fn (Container $container): UpdatePet => new UpdatePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(LoginUser::class, static fn (Container $container): LoginUser => new LoginUser(
            $container->get(PdoUserRepository::class)
        ));

        $container->set(AuthorizationService::class, static fn () => new AuthorizationService());

        $container->set(GetAuthenticatedUser::class, static fn (Container $container): GetAuthenticatedUser => new GetAuthenticatedUser(
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(HealthController::class, static fn (Container $container): HealthController => new HealthController(
            $container->get(GetHealthStatus::class)
        ));

        $container->set(RegisterUserController::class, static fn (Container $container): RegisterUserController => new RegisterUserController(
            $container->get(RegisterUser::class)
        ));

        $container->set(LoginUserController::class, static fn (Container $container): LoginUserController => new LoginUserController(
            $container->get(LoginUser::class),
            $container->get(SessionManager::class)
        ));

        $container->set(LogoutUserController::class, static fn (Container $container): LogoutUserController => new LogoutUserController(
            $container->get(SessionManager::class)
        ));

        $container->set(AuthenticatedUserController::class, static fn (Container $container): AuthenticatedUserController => new AuthenticatedUserController(
            $container->get(GetAuthenticatedUser::class)
        ));

        $container->set(CreateOrganizationController::class, static fn (Container $container): CreateOrganizationController => new CreateOrganizationController(
            $container->get(CreateOrganization::class)
        ));

        $container->set(ListPetsController::class, static fn (Container $container): ListPetsController => new ListPetsController(
            $container->get(ListPets::class)
        ));

        $container->set(GetPetController::class, static fn (Container $container): GetPetController => new GetPetController(
            $container->get(GetPet::class)
        ));

        $container->set(CreatePetController::class, static fn (Container $container): CreatePetController => new CreatePetController(
            $container->get(CreatePet::class)
        ));

        $container->set(ArchivePetController::class, static fn (Container $container): ArchivePetController => new ArchivePetController(
            $container->get(ArchivePet::class)
        ));

        $container->set(AddPetPhotoController::class, static fn (Container $container): AddPetPhotoController => new AddPetPhotoController(
            $container->get(AddPetPhoto::class)
        ));

        $container->set(RemovePetPhotoController::class, static fn (Container $container): RemovePetPhotoController => new RemovePetPhotoController(
            $container->get(RemovePetPhoto::class)
        ));

        $container->set(UpdatePetController::class, static fn (Container $container): UpdatePetController => new UpdatePetController(
            $container->get(UpdatePet::class)
        ));

        $container->set(Router::class, static fn (Container $container): Router => new Router([
            'GET /health' => $container->get(HealthController::class),
            'GET /' => $container->get(HealthController::class),
            'POST /api/v1/auth/register' => $container->get(RegisterUserController::class),
            'POST /api/v1/auth/login' => $container->get(LoginUserController::class),
            'GET /api/v1/auth/me' => $container->get(AuthenticatedUserController::class),
            'POST /api/v1/auth/logout' => $container->get(LogoutUserController::class),
            'POST /api/v1/organizations' => $container->get(CreateOrganizationController::class),
            'GET /api/v1/pets' => $container->get(ListPetsController::class),
            'GET /api/v1/pets/{id}' => $container->get(GetPetController::class),
            'POST /api/v1/pets' => $container->get(CreatePetController::class),
            'PUT /api/v1/pets/{id}' => $container->get(UpdatePetController::class),
            'PATCH /api/v1/pets/{id}/archive' => $container->get(ArchivePetController::class),
            'POST /api/v1/pets/{id}/photos' => $container->get(AddPetPhotoController::class),
            'DELETE /api/v1/pets/{id}/photos/{photoId}' => $container->get(RemovePetPhotoController::class),
        ]));

        $container->set(ApplicationKernel::class, static fn (Container $container): ApplicationKernel => new ApplicationKernel(
            $container->get(Router::class)
        ));

        return $container->get(ApplicationKernel::class);
    }
}
