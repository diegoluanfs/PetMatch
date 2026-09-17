<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Application\Pet\UpdatePet;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class UpdatePetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_updates_pet_for_own_organization(): void
    {
        $userRepository = new InMemoryUserRepository();
        $organizationUserId = $userRepository->save(new User(
            null,
            1,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petRepository->save(new Pet(
            null,
            1,
            'Thor',
            'Labrador amigável',
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

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new UpdatePet($petRepository, $userRepository, $sessionManager);
        $result = $useCase->execute(3, [
            'name' => 'Thor Atualizado',
            'description' => 'Labrador ainda mais amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'birth_date' => '2022-01-01',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);

        self::assertSame('Thor Atualizado', $result['name']);
        self::assertSame('Labrador ainda mais amigável', $result['description']);
    }

    public function test_rejects_pet_from_other_organization(): void
    {
        $userRepository = new InMemoryUserRepository();
        $organizationUserId = $userRepository->save(new User(
            null,
            1,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petRepository->save(new Pet(
            null,
            2,
            'Thor',
            'Labrador amigável',
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

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new UpdatePet($petRepository, $userRepository, $sessionManager);

        $this->expectException(ForbiddenException::class);

        $useCase->execute(3, [
            'name' => 'Thor Atualizado',
            'description' => 'Labrador ainda mais amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);
    }

    public function test_rejects_missing_pet(): void
    {
        $userRepository = new InMemoryUserRepository();
        $organizationUserId = $userRepository->save(new User(
            null,
            1,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new UpdatePet(new InMemoryPetRepository(), $userRepository, $sessionManager);

        $this->expectException(PetNotFoundException::class);

        $useCase->execute(999, [
            'name' => 'Thor Atualizado',
            'description' => 'Labrador ainda mais amigável',
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
        $organizationUserId = $userRepository->save(new User(
            null,
            1,
            'ONG Admin',
            'ong.admin@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petRepository->save(new Pet(
            null,
            1,
            'Thor',
            'Labrador amigável',
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

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new UpdatePet($petRepository, $userRepository, $sessionManager);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute(3, [
            'name' => '',
            'description' => '',
            'animal_type' => '',
            'breed' => '',
            'size' => '',
            'city' => '',
            'state' => '',
        ]);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new UpdatePet(new InMemoryPetRepository(), new InMemoryUserRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute(1, [
            'name' => 'Thor Atualizado',
            'description' => 'Labrador ainda mais amigável',
            'animal_type' => 'dog',
            'breed' => 'labrador',
            'gender' => 'male',
            'size' => 'large',
            'city' => 'Santa Maria',
            'state' => 'RS',
        ]);
    }
}
