<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Auth;

use InvalidArgumentException;
use PetMatch\Application\Auth\EmailAlreadyRegisteredException;
use PetMatch\Application\Auth\RegisterUser;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class RegisterUserTest extends TestCase
{
    public function test_registers_a_user_with_pending_status(): void
    {
        $repository = new InMemoryUserRepository();
        $useCase = new RegisterUser($repository);

        $result = $useCase->execute('Diego', 'Diego.Teste@example.com', 'secret123');

        self::assertSame(1, $result['id']);
        self::assertSame('Diego', $result['name']);
        self::assertSame('diego.teste@example.com', $result['email']);
        self::assertSame('adopter', $result['role']);
        self::assertSame('pending', $result['status']);
    }

    public function test_rejects_duplicate_email(): void
    {
        $repository = new InMemoryUserRepository();
        $useCase = new RegisterUser($repository);
        $useCase->execute('Diego', 'diego.teste@example.com', 'secret123');

        $this->expectException(EmailAlreadyRegisteredException::class);

        $useCase->execute('Maria', 'diego.teste@example.com', 'secret123');
    }

    public function test_rejects_invalid_input(): void
    {
        $repository = new InMemoryUserRepository();
        $useCase = new RegisterUser($repository);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute('', 'invalid-email', '123');
    }
}
