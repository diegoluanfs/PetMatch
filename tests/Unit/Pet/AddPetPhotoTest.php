<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\AddPetPhoto;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetPhotoRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class AddPetPhotoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_adds_photo_to_own_pet(): void
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
        $petPhotoRepository = new InMemoryPetPhotoRepository();

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new AddPetPhoto($petRepository, $petPhotoRepository, $userRepository, $sessionManager);
        $result = $useCase->execute(1, [
            'path' => '/storage/pets/thor-1.jpg',
            'sort_order' => 1,
        ]);

        self::assertSame(1, $result['id']);
        self::assertSame(1, $result['pet_id']);
        self::assertSame('/storage/pets/thor-1.jpg', $result['path']);
        self::assertSame(1, $result['sort_order']);
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

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($userId);
        $sessionManager->setUserRole('adopter');

        $useCase = new AddPetPhoto(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), $userRepository, $sessionManager);

        $this->expectException(ForbiddenException::class);

        $useCase->execute(1, [
            'path' => '/storage/pets/thor-1.jpg',
        ]);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new AddPetPhoto(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), new InMemoryUserRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute(1, [
            'path' => '/storage/pets/thor-1.jpg',
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

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new AddPetPhoto(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), $userRepository, $sessionManager);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute(1, [
            'path' => '',
        ]);
    }
}
