<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\ArchivePet;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class ArchivePetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_archives_pet_from_own_organization(): void
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

        $useCase = new ArchivePet($petRepository, $userRepository, $sessionManager);
        $result = $useCase->execute(3);

        self::assertSame('archived', $result['status']);
    }

    public function test_rejects_other_organization_pets(): void
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

        $useCase = new ArchivePet($petRepository, $userRepository, $sessionManager);

        $this->expectException(ForbiddenException::class);

        $useCase->execute(3);
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

        $useCase = new ArchivePet(new InMemoryPetRepository(), $userRepository, $sessionManager);

        $this->expectException(PetNotFoundException::class);

        $useCase->execute(999);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new ArchivePet(new InMemoryPetRepository(), new InMemoryUserRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute(1);
    }
}
