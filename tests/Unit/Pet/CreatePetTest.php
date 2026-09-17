<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\CreatePet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class CreatePetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_creates_pet_for_organization_admin(): void
    {
        $userRepository = new InMemoryUserRepository();
        $organizationUserId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new CreatePet($petRepository, $userRepository, $sessionManager);
        $result = $useCase->execute([
            'name' => 'Thor',
            'description' => 'Labrador amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'birth_date' => '2022-01-01',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);

        self::assertSame(3, $result['id']);
        self::assertSame(99, $result['organization_id']);
        self::assertSame('available', $result['status']);
    }

    public function test_rejects_non_organization_users(): void
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
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('adopter');

        $useCase = new CreatePet($petRepository, $userRepository, $sessionManager);

        $this->expectException(ForbiddenException::class);

        $useCase->execute([
            'name' => 'Thor',
            'description' => 'Labrador amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new CreatePet(new InMemoryPetRepository(), new InMemoryUserRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute([
            'name' => 'Thor',
            'description' => 'Labrador amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);
    }

    public function test_rejects_invalid_input(): void
    {
        $userRepository = new InMemoryUserRepository();
        $userId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new CreatePet($petRepository, $userRepository, $sessionManager);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute([
            'name' => '',
            'description' => '',
            'animal_type' => '',
            'breed' => '',
            'size' => '',
            'city' => '',
            'state' => '',
        ]);
    }
}
