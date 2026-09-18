<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\ListOrganizationPets;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryPetPhotoRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class ListOrganizationPetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_lists_available_and_archived_pets_for_own_organization(): void
    {
        $users = new InMemoryUserRepository();
        $adminId = $users->save(new User(null, 1, 'ONG', 'ong@example.com', 'hash', 'organization_admin', 'active'));
        $pets = new InMemoryPetRepository();
        $pets->save(new Pet(null, 1, 'Arquivado', 'Histórico', 'dog', 'mixed', 'unknown', null, 'medium', 'archived', 'Santa Maria', 'RS', null, null));

        $session = new SessionManager();
        $session->setUserId($adminId);

        $useCase = new ListOrganizationPets($pets, new InMemoryPetPhotoRepository(), $users, $session);
        $result = $useCase->execute();

        self::assertCount(3, $result);
        self::assertContains('archived', array_column($result, 'status'));
    }

    public function test_rejects_adopter(): void
    {
        $users = new InMemoryUserRepository();
        $userId = $users->save(new User(null, null, 'Adopter', 'adopter@example.com', 'hash', 'adopter', 'active'));
        $session = new SessionManager();
        $session->setUserId($userId);

        $this->expectException(ForbiddenException::class);
        $useCase = new ListOrganizationPets(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), $users, $session);
        $useCase->execute();
    }

    public function test_requires_authentication(): void
    {
        $this->expectException(NotAuthenticatedException::class);
        $useCase = new ListOrganizationPets(new InMemoryPetRepository(), new InMemoryPetPhotoRepository(), new InMemoryUserRepository(), new SessionManager());
        $useCase->execute();
    }
}
