<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Adoption;

use PetMatch\Application\Adoption\ListAdoptionRequests;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryAdoptionRequestRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class ListAdoptionRequestsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_lists_only_current_user_requests(): void
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

        $otherUserId = $userRepository->save(new User(
            null,
            null,
            'Pedro',
            'pedro@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'active',
        ));

        $requestRepository = new InMemoryAdoptionRequestRepository();
        $requestRepository->saveFixture([
            'id' => 1,
            'user_id' => $userId,
            'pet_id' => 1,
            'status' => 'pending',
            'message' => 'Quero adotar o Thor',
        ]);
        $requestRepository->saveFixture([
            'id' => 2,
            'user_id' => $otherUserId,
            'pet_id' => 1,
            'status' => 'pending',
            'message' => 'Outro pedido',
        ]);

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('adopter');

        $useCase = new ListAdoptionRequests($requestRepository, $sessionManager);

        $result = $useCase->execute();

        self::assertCount(1, $result);
        self::assertSame($userId, $result[0]['user_id']);
        self::assertSame(1, $result[0]['pet_id']);
    }
}
