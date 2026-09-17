<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Auth;

use PetMatch\Application\Auth\GetAuthenticatedUser;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class GetAuthenticatedUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_returns_the_current_user_from_session(): void
    {
        $repository = new InMemoryUserRepository();
        $userId = $repository->save(new User(
            null,
            'Maria',
            'maria.teste@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'pending',
        ));

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);

        $useCase = new GetAuthenticatedUser($repository, $sessionManager);
        $result = $useCase->execute();

        self::assertSame($userId, $result['id']);
        self::assertSame('Maria', $result['name']);
        self::assertSame('maria.teste@example.com', $result['email']);
    }

    public function test_rejects_missing_session_user(): void
    {
        $repository = new InMemoryUserRepository();
        $sessionManager = new SessionManager();
        $useCase = new GetAuthenticatedUser($repository, $sessionManager);

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute();
    }
}
