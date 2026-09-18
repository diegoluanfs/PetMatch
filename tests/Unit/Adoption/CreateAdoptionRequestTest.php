<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Adoption;

use PetMatch\Application\Adoption\AdoptionRequestAlreadyExistsException;
use PetMatch\Application\Adoption\CreateAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryAdoptionRequestRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PetMatch\Tests\Support\InMemoryUserVerificationRepository;
use PHPUnit\Framework\TestCase;

final class CreateAdoptionRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_creates_adoption_request_for_available_pet(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userId = $userRepository->save(new User(
            null,
            null,
            'Maria',
            'maria@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $requestRepository = new InMemoryAdoptionRequestRepository();
        $verificationRepository = new InMemoryUserVerificationRepository();
        $verificationRepository->verify($userId, 'email');
        $verificationRepository->verify($userId, 'whatsapp');
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('adopter');

        $useCase = new CreateAdoptionRequest(
            $petRepository,
            $requestRepository,
            $userRepository,
            $sessionManager,
            $verificationRepository,
        );

        $result = $useCase->execute([
            'pet_id' => 1,
            'message' => 'Quero adotar o Thor.',
        ]);

        self::assertSame(1, $result['id']);
        self::assertSame(1, $result['user_id']);
        self::assertSame(1, $result['pet_id']);
        self::assertSame('pending', $result['status']);
        self::assertSame('Quero adotar o Thor.', $result['message']);
    }

    public function test_rejects_unverified_adopter(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userId = $userRepository->save(new User(null, null, 'Maria', 'maria@example.com', 'hash', 'adopter', 'active'));
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);

        $this->expectException(ForbiddenException::class);

        $useCase = new CreateAdoptionRequest(
            new InMemoryPetRepository(),
            new InMemoryAdoptionRequestRepository(),
            $userRepository,
            $sessionManager,
            new InMemoryUserVerificationRepository(),
        );
        $useCase->execute(['pet_id' => 1, 'message' => 'Quero adotar.']);
    }

    public function test_rejects_organization_user(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $requestRepository = new InMemoryAdoptionRequestRepository();
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new CreateAdoptionRequest(
            $petRepository,
            $requestRepository,
            $userRepository,
            $sessionManager,
        );

        $this->expectException(ForbiddenException::class);

        $useCase->execute([
            'pet_id' => 1,
            'message' => 'Gostaria de adotar.',
        ]);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new CreateAdoptionRequest(
            new InMemoryPetRepository(),
            new InMemoryAdoptionRequestRepository(),
            new InMemoryUserRepository(),
            new SessionManager(),
        );

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute([
            'pet_id' => 1,
            'message' => 'Quero adotar.',
        ]);
    }

    public function test_rejects_duplicate_active_request(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userId = $userRepository->save(new User(
            null,
            null,
            'Maria',
            'maria@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $requestRepository = new InMemoryAdoptionRequestRepository();
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('adopter');

        $requestRepository->saveFixture([
            'id' => 1,
            'user_id' => $userId,
            'pet_id' => 1,
            'status' => 'pending',
            'message' => 'Primeira solicitação',
        ]);

        $useCase = new CreateAdoptionRequest(
            $petRepository,
            $requestRepository,
            $userRepository,
            $sessionManager,
        );

        $this->expectException(AdoptionRequestAlreadyExistsException::class);

        $useCase->execute([
            'pet_id' => 1,
            'message' => 'Segunda solicitação',
        ]);
    }
}
