<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Adoption;

use PetMatch\Application\Adoption\ApproveAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryAdoptionRequestRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class ApproveAdoptionRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_approves_pending_request_for_own_pet(): void
    {
        $userRepository = new InMemoryUserRepository();
        $orgAdminId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petId = $petRepository->save(new Pet(
            null,
            99,
            'Thor',
            'Amigável',
            'dog',
            'labrador',
            'male',
            '2022-01-01',
            'large',
            'available',
            'Santa Maria',
            'RS',
            null,
            null,
        ));

        $requestRepository = new InMemoryAdoptionRequestRepository();
        $requestRepository->saveFixture([
            'id' => 1,
            'user_id' => 2,
            'pet_id' => $petId,
            'status' => 'pending',
            'message' => 'Quero adotar',
        ]);

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($orgAdminId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new ApproveAdoptionRequest(
            $petRepository,
            $requestRepository,
            $userRepository,
            $sessionManager,
        );

        $result = $useCase->execute(1);

        self::assertSame(1, $result['id']);
        self::assertSame('approved', $result['status']);
        self::assertSame('adopted', $petRepository->findById($petId)->status);
    }

    public function test_rejects_request_from_non_owner(): void
    {
        $userRepository = new InMemoryUserRepository();
        $orgAdminId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petId = $petRepository->save(new Pet(
            null,
            50,
            'Luna',
            'Carinhosa',
            'dog',
            'vira-lata',
            'female',
            '2023-02-15',
            'medium',
            'available',
            'Porto Alegre',
            'RS',
            null,
            null,
        ));

        $requestRepository = new InMemoryAdoptionRequestRepository();
        $requestRepository->saveFixture([
            'id' => 1,
            'user_id' => 2,
            'pet_id' => $petId,
            'status' => 'pending',
            'message' => 'Quero adotar',
        ]);

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($orgAdminId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new ApproveAdoptionRequest(
            $petRepository,
            $requestRepository,
            $userRepository,
            $sessionManager,
        );

        $this->expectException(ForbiddenException::class);

        $useCase->execute(1);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new ApproveAdoptionRequest(
            new InMemoryPetRepository(),
            new InMemoryAdoptionRequestRepository(),
            new InMemoryUserRepository(),
            new SessionManager(),
        );

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute(1);
    }
}
