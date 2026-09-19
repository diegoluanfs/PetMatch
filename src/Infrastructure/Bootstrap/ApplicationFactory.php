<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Bootstrap;

use PDO;
use PetMatch\Application\Adoption\ApproveAdoptionRequest;
use PetMatch\Application\Adoption\CreateAdoptionRequest;
use PetMatch\Application\Adoption\ListAdoptionRequests;
use PetMatch\Application\Adoption\ListOrganizationAdoptionRequests;
use PetMatch\Application\Adoption\RejectAdoptionRequest;
use PetMatch\Application\Adoption\WithdrawAdoptionRequest;
use PetMatch\Application\Engagement\SwipePet;
use PetMatch\Application\Engagement\ListLikedPets;
use PetMatch\Application\Engagement\CreateFavorite;
use PetMatch\Application\Engagement\RemoveFavorite;
use PetMatch\Application\Engagement\ListFavorites;
use PetMatch\Application\System\GetHealthStatus;
use PetMatch\Application\Auth\RegisterUser;
use PetMatch\Application\Auth\GetAuthenticatedUser;
use PetMatch\Application\Auth\AuthorizationService;
use PetMatch\Application\Auth\LoginUser;
use PetMatch\Application\Auth\ListUserVerifications;
use PetMatch\Application\Organization\CreateOrganization;
use PetMatch\Application\Pet\GetPet;
use PetMatch\Application\Pet\CreatePet;
use PetMatch\Application\Pet\ArchivePet;
use PetMatch\Application\Pet\AddPetPhoto;
use PetMatch\Application\Pet\RemovePetPhoto;
use PetMatch\Application\Pet\ListPets;
use PetMatch\Application\Pet\ListOrganizationPets;
use PetMatch\Application\Pet\UpdatePet;
use PetMatch\Infrastructure\Container\Container;
use PetMatch\Infrastructure\Database\DatabaseConnection;
use PetMatch\Infrastructure\Database\PdoTransactionManager;
use PetMatch\Infrastructure\Http\ApplicationKernel;
use PetMatch\Infrastructure\Http\Router;
use PetMatch\Infrastructure\Persistence\PdoAdoptionRequestRepository;
use PetMatch\Infrastructure\Persistence\PdoPetRepository;
use PetMatch\Infrastructure\Persistence\PdoPetPhotoRepository;
use PetMatch\Infrastructure\Persistence\PdoOrganizationRepository;
use PetMatch\Infrastructure\Persistence\PdoUserRepository;
use PetMatch\Infrastructure\Persistence\PdoUserVerificationRepository;
use PetMatch\Infrastructure\Persistence\PdoSwipeRepository;
use PetMatch\Infrastructure\Persistence\PdoFavoriteRepository;
use PetMatch\Infrastructure\Storage\LocalPetPhotoStorage;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Presentation\Controllers\HealthController;
use PetMatch\Presentation\Controllers\DashboardController;
use PetMatch\Presentation\Controllers\PublicHomeController;
use PetMatch\Presentation\Controllers\AuthenticatedUserController;
use PetMatch\Presentation\Controllers\GetPetController;
use PetMatch\Presentation\Controllers\CreatePetController;
use PetMatch\Presentation\Controllers\ArchivePetController;
use PetMatch\Presentation\Controllers\AddPetPhotoController;
use PetMatch\Presentation\Controllers\RemovePetPhotoController;
use PetMatch\Presentation\Controllers\UpdatePetController;
use PetMatch\Presentation\Controllers\LoginUserController;
use PetMatch\Presentation\Controllers\ListUserVerificationsController;
use PetMatch\Presentation\Controllers\LogoutUserController;
use PetMatch\Presentation\Controllers\ListPetsController;
use PetMatch\Presentation\Controllers\ListOrganizationPetsController;
use PetMatch\Presentation\Controllers\RegisterUserController;
use PetMatch\Presentation\Controllers\CreateOrganizationController;
use PetMatch\Presentation\Controllers\ApproveAdoptionRequestController;
use PetMatch\Presentation\Controllers\CreateAdoptionRequestController;
use PetMatch\Presentation\Controllers\ListAdoptionRequestsController;
use PetMatch\Presentation\Controllers\ListOrganizationAdoptionRequestsController;
use PetMatch\Presentation\Controllers\RejectAdoptionRequestController;
use PetMatch\Presentation\Controllers\WithdrawAdoptionRequestController;
use PetMatch\Presentation\Controllers\SwipePetController;
use PetMatch\Presentation\Controllers\ListLikedPetsController;
use PetMatch\Presentation\Controllers\CreateFavoriteController;
use PetMatch\Presentation\Controllers\RemoveFavoriteController;
use PetMatch\Presentation\Controllers\ListFavoritesController;

final class ApplicationFactory
{
    public function createKernel(): ApplicationKernel
    {
        $container = new Container();

        $container->set(DatabaseConnection::class, static fn () => new DatabaseConnection());

        $container->set(PDO::class, static fn (Container $container): PDO => $container->get(DatabaseConnection::class)->create());

        $container->set(PdoTransactionManager::class, static fn (Container $container): PdoTransactionManager => new PdoTransactionManager(
            $container->get(PDO::class)
        ));

        $container->set(SessionManager::class, static fn () => new SessionManager());

        $container->set(GetHealthStatus::class, static fn () => new GetHealthStatus());

        $container->set(PdoUserRepository::class, static fn (Container $container): PdoUserRepository => new PdoUserRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoUserVerificationRepository::class, static fn (Container $container): PdoUserVerificationRepository => new PdoUserVerificationRepository(
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

        $container->set(PdoAdoptionRequestRepository::class, static fn (Container $container): PdoAdoptionRequestRepository => new PdoAdoptionRequestRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoSwipeRepository::class, static fn (Container $container): PdoSwipeRepository => new PdoSwipeRepository(
            $container->get(PDO::class)
        ));

        $container->set(PdoFavoriteRepository::class, static fn (Container $container): PdoFavoriteRepository => new PdoFavoriteRepository(
            $container->get(PDO::class)
        ));

        $container->set(LocalPetPhotoStorage::class, static fn (): LocalPetPhotoStorage => new LocalPetPhotoStorage(
            dirname(__DIR__, 3) . '/public/storage/pet-photos'
        ));

        $container->set(RegisterUser::class, static fn (Container $container): RegisterUser => new RegisterUser(
            $container->get(PdoUserRepository::class)
        ));

        $container->set(CreateOrganization::class, static fn (Container $container): CreateOrganization => new CreateOrganization(
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(ListPets::class, static fn (Container $container): ListPets => new ListPets(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class)
        ));

        $container->set(ListOrganizationPets::class, static fn (Container $container): ListOrganizationPets => new ListOrganizationPets(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(GetPet::class, static fn (Container $container): GetPet => new GetPet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class)
        ));

        $container->set(CreatePet::class, static fn (Container $container): CreatePet => new CreatePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(ArchivePet::class, static fn (Container $container): ArchivePet => new ArchivePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(AddPetPhoto::class, static fn (Container $container): AddPetPhoto => new AddPetPhoto(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(RemovePetPhoto::class, static fn (Container $container): RemovePetPhoto => new RemovePetPhoto(
            $container->get(PdoPetRepository::class),
            $container->get(PdoPetPhotoRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(UpdatePet::class, static fn (Container $container): UpdatePet => new UpdatePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(CreateAdoptionRequest::class, static fn (Container $container): CreateAdoptionRequest => new CreateAdoptionRequest(
            $container->get(PdoPetRepository::class),
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoUserVerificationRepository::class)
        ));

        $container->set(ListAdoptionRequests::class, static fn (Container $container): ListAdoptionRequests => new ListAdoptionRequests(
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(ListOrganizationAdoptionRequests::class, static fn (Container $container): ListOrganizationAdoptionRequests => new ListOrganizationAdoptionRequests(
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(ApproveAdoptionRequest::class, static fn (Container $container): ApproveAdoptionRequest => new ApproveAdoptionRequest(
            $container->get(PdoPetRepository::class),
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoTransactionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(RejectAdoptionRequest::class, static fn (Container $container): RejectAdoptionRequest => new RejectAdoptionRequest(
            $container->get(PdoPetRepository::class),
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(PdoUserRepository::class),
            $container->get(SessionManager::class),
            $container->get(PdoOrganizationRepository::class)
        ));

        $container->set(WithdrawAdoptionRequest::class, static fn (Container $container): WithdrawAdoptionRequest => new WithdrawAdoptionRequest(
            $container->get(PdoAdoptionRequestRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(SwipePet::class, static fn (Container $container): SwipePet => new SwipePet(
            $container->get(PdoPetRepository::class),
            $container->get(PdoSwipeRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(ListLikedPets::class, static fn (Container $container): ListLikedPets => new ListLikedPets(
            $container->get(PdoSwipeRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(CreateFavorite::class, static fn (Container $container): CreateFavorite => new CreateFavorite(
            $container->get(PdoPetRepository::class),
            $container->get(PdoFavoriteRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(RemoveFavorite::class, static fn (Container $container): RemoveFavorite => new RemoveFavorite(
            $container->get(PdoFavoriteRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(ListFavorites::class, static fn (Container $container): ListFavorites => new ListFavorites(
            $container->get(PdoFavoriteRepository::class),
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

        $container->set(ListUserVerifications::class, static fn (Container $container): ListUserVerifications => new ListUserVerifications(
            $container->get(PdoUserVerificationRepository::class),
            $container->get(SessionManager::class)
        ));

        $container->set(HealthController::class, static fn (Container $container): HealthController => new HealthController(
            $container->get(GetHealthStatus::class)
        ));

        $container->set(DashboardController::class, static fn () => new DashboardController());

        $container->set(PublicHomeController::class, static fn () => new PublicHomeController());

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

        $container->set(ListUserVerificationsController::class, static fn (Container $container): ListUserVerificationsController => new ListUserVerificationsController(
            $container->get(ListUserVerifications::class)
        ));

        $container->set(CreateOrganizationController::class, static fn (Container $container): CreateOrganizationController => new CreateOrganizationController(
            $container->get(CreateOrganization::class)
        ));

        $container->set(ListPetsController::class, static fn (Container $container): ListPetsController => new ListPetsController(
            $container->get(ListPets::class)
        ));

        $container->set(ListOrganizationPetsController::class, static fn (Container $container): ListOrganizationPetsController => new ListOrganizationPetsController(
            $container->get(ListOrganizationPets::class)
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
            $container->get(AddPetPhoto::class),
            $container->get(LocalPetPhotoStorage::class)
        ));

        $container->set(RemovePetPhotoController::class, static fn (Container $container): RemovePetPhotoController => new RemovePetPhotoController(
            $container->get(RemovePetPhoto::class)
        ));

        $container->set(UpdatePetController::class, static fn (Container $container): UpdatePetController => new UpdatePetController(
            $container->get(UpdatePet::class)
        ));

        $container->set(CreateAdoptionRequestController::class, static fn (Container $container): CreateAdoptionRequestController => new CreateAdoptionRequestController(
            $container->get(CreateAdoptionRequest::class)
        ));

        $container->set(ListAdoptionRequestsController::class, static fn (Container $container): ListAdoptionRequestsController => new ListAdoptionRequestsController(
            $container->get(ListAdoptionRequests::class)
        ));

        $container->set(ListOrganizationAdoptionRequestsController::class, static fn (Container $container): ListOrganizationAdoptionRequestsController => new ListOrganizationAdoptionRequestsController(
            $container->get(ListOrganizationAdoptionRequests::class)
        ));

        $container->set(ApproveAdoptionRequestController::class, static fn (Container $container): ApproveAdoptionRequestController => new ApproveAdoptionRequestController(
            $container->get(ApproveAdoptionRequest::class)
        ));

        $container->set(RejectAdoptionRequestController::class, static fn (Container $container): RejectAdoptionRequestController => new RejectAdoptionRequestController(
            $container->get(RejectAdoptionRequest::class)
        ));

        $container->set(WithdrawAdoptionRequestController::class, static fn (Container $container): WithdrawAdoptionRequestController => new WithdrawAdoptionRequestController(
            $container->get(WithdrawAdoptionRequest::class)
        ));

        $container->set(SwipePetController::class, static fn (Container $container): SwipePetController => new SwipePetController(
            $container->get(SwipePet::class)
        ));

        $container->set(ListLikedPetsController::class, static fn (Container $container): ListLikedPetsController => new ListLikedPetsController(
            $container->get(ListLikedPets::class)
        ));

        $container->set(CreateFavoriteController::class, static fn (Container $container): CreateFavoriteController => new CreateFavoriteController($container->get(CreateFavorite::class)));
        $container->set(RemoveFavoriteController::class, static fn (Container $container): RemoveFavoriteController => new RemoveFavoriteController($container->get(RemoveFavorite::class)));
        $container->set(ListFavoritesController::class, static fn (Container $container): ListFavoritesController => new ListFavoritesController($container->get(ListFavorites::class)));

        $container->set(Router::class, static fn (Container $container): Router => new Router([
            'GET /health' => $container->get(HealthController::class),
            'GET /' => $container->get(PublicHomeController::class),
            'GET /playground' => $container->get(DashboardController::class),
            'POST /api/v1/auth/register' => $container->get(RegisterUserController::class),
            'POST /api/v1/auth/login' => $container->get(LoginUserController::class),
            'GET /api/v1/auth/me' => $container->get(AuthenticatedUserController::class),
            'GET /api/v1/auth/verifications' => $container->get(ListUserVerificationsController::class),
            'POST /api/v1/auth/logout' => $container->get(LogoutUserController::class),
            'POST /api/v1/organizations' => $container->get(CreateOrganizationController::class),
            'GET /api/v1/pets' => $container->get(ListPetsController::class),
            'GET /api/v1/organizations/pets' => $container->get(ListOrganizationPetsController::class),
            'GET /api/v1/pets/{id}' => $container->get(GetPetController::class),
            'POST /api/v1/pets' => $container->get(CreatePetController::class),
            'PUT /api/v1/pets/{id}' => $container->get(UpdatePetController::class),
            'PATCH /api/v1/pets/{id}/archive' => $container->get(ArchivePetController::class),
            'POST /api/v1/pets/{id}/photos' => $container->get(AddPetPhotoController::class),
            'DELETE /api/v1/pets/{id}/photos/{photoId}' => $container->get(RemovePetPhotoController::class),
            'POST /api/v1/adoption-requests' => $container->get(CreateAdoptionRequestController::class),
            'GET /api/v1/adoption-requests' => $container->get(ListAdoptionRequestsController::class),
            'GET /api/v1/organizations/adoption-requests' => $container->get(ListOrganizationAdoptionRequestsController::class),
            'POST /api/v1/pets/{id}/swipe' => $container->get(SwipePetController::class),
            'GET /api/v1/me/liked-pets' => $container->get(ListLikedPetsController::class),
            'GET /api/v1/me/favorites' => $container->get(ListFavoritesController::class),
            'POST /api/v1/pets/{id}/favorite' => $container->get(CreateFavoriteController::class),
            'DELETE /api/v1/pets/{id}/favorite' => $container->get(RemoveFavoriteController::class),
            'PATCH /api/v1/adoption-requests/{id}/approve' => $container->get(ApproveAdoptionRequestController::class),
            'PATCH /api/v1/adoption-requests/{id}/reject' => $container->get(RejectAdoptionRequestController::class),
            'PATCH /api/v1/adoption-requests/{id}/withdraw' => $container->get(WithdrawAdoptionRequestController::class),
        ]));

        $container->set(ApplicationKernel::class, static fn (Container $container): ApplicationKernel => new ApplicationKernel(
            $container->get(Router::class)
        ));

        return $container->get(ApplicationKernel::class);
    }
}
