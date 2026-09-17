<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Auth;

use PetMatch\Application\Auth\InvalidCredentialsException;
use PetMatch\Application\Auth\LoginUser;
use PetMatch\Domain\User\User;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class LoginUserTest extends TestCase
{
    public function test_logs_in_with_valid_credentials(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(new User(
            null,
            'Maria',
            'maria.teste@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'pending',
        ));

        $useCase = new LoginUser($repository);
        $result = $useCase->execute('maria.teste@example.com', 'secret123');

        self::assertSame('Maria', $result['name']);
        self::assertSame('maria.teste@example.com', $result['email']);
        self::assertSame('adopter', $result['role']);
        self::assertSame('pending', $result['status']);
    }

    public function test_rejects_invalid_credentials(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->save(new User(
            null,
            'Maria',
            'maria.teste@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'adopter',
            'pending',
        ));

        $useCase = new LoginUser($repository);

        $this->expectException(InvalidCredentialsException::class);

        $useCase->execute('maria.teste@example.com', 'wrongpass');
    }
}
