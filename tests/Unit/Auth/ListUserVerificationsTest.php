<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Auth;

use PetMatch\Application\Auth\ListUserVerifications;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryUserVerificationRepository;
use PHPUnit\Framework\TestCase;

final class ListUserVerificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_lists_only_current_user_verifications(): void
    {
        $repository = new InMemoryUserVerificationRepository();
        $repository->verify(7, 'email');
        $repository->verify(8, 'whatsapp');
        $session = new SessionManager();
        $session->setUserId(7);

        $useCase = new ListUserVerifications($repository, $session);
        $result = $useCase->execute();

        self::assertCount(1, $result);
        self::assertSame('email', $result[0]['type']);
        self::assertSame('verified', $result[0]['status']);
    }

    public function test_requires_authentication(): void
    {
        $this->expectException(NotAuthenticatedException::class);
        $useCase = new ListUserVerifications(new InMemoryUserVerificationRepository(), new SessionManager());
        $useCase->execute();
    }
}
