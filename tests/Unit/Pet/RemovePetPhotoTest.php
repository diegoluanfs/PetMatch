<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\PetPhotoNotFoundException;
use PetMatch\Application\Pet\RemovePetPhoto;
use PetMatch\Domain\Pet\PetPhoto;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetPhotoRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;

final class RemovePetPhotoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_removes_photo_from_own_pet(): void
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
        $photoId = $petPhotoRepository->save(new PetPhoto(null, 1, '/storage/pets/thor-1.jpg', 0));

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new RemovePetPhoto($petRepository, $petPhotoRepository, $userRepository, $sessionManager);
        $result = $useCase->execute(1, $photoId);

        self::assertSame($photoId, $result['id']);
        self::assertSame(1, $result['pet_id']);
        self::assertNull($petPhotoRepository->findById($photoId));
    }

    public function test_rejects_photo_that_does_not_belong_to_pet(): void
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
        $photoId = $petPhotoRepository->save(new PetPhoto(null, 2, '/storage/pets/luna-1.jpg', 0));

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($organizationUserId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new RemovePetPhoto($petRepository, $petPhotoRepository, $userRepository, $sessionManager);

        $this->expectException(PetPhotoNotFoundException::class);

        $useCase->execute(1, $photoId);
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

        $useCase = new RemovePetPhoto(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), $userRepository, $sessionManager);

        $this->expectException(ForbiddenException::class);

        $useCase->execute(1, 1);
    }

    public function test_rejects_missing_authentication(): void
    {
        $useCase = new RemovePetPhoto(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), new InMemoryUserRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);

        $useCase->execute(1, 1);
    }
}
