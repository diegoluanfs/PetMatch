<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Adoption;

use PetMatch\Application\Adoption\ListOrganizationAdoptionRequests;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryAdoptionRequestRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class ListOrganizationAdoptionRequestsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_lists_only_requests_for_current_organization(): void
    {
        $userRepository = new InMemoryUserRepository();
        $orgAdminId = $userRepository->save(new User(
            null,
            99,
            'ONG Admin',
            'ong@example.com',
            password_hash('secret123', PASSWORD_DEFAULT),
            'organization_admin',
            'active',
        ));

        $petRepository = new InMemoryPetRepository();
        $petIdA = $petRepository->save(new Pet(
            null,
            99,
            'Thor',
            'Dócil',
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

        $petIdB = $petRepository->save(new Pet(
            null,
            50,
            'Luna',
            'Carinhosa',
            'dog',
            'vira-lata',
            'female',
            '2023-02-15',
            'medium',
            'available',
            'Porto Alegre',
            'RS',
            null,
            null,
        ));

        $requestRepository = new InMemoryAdoptionRequestRepository();
        $requestRepository->setPetRepository($petRepository);
        $requestRepository->saveFixture([
            'id' => 1,
            'user_id' => 2,
            'pet_id' => $petIdA,
            'status' => 'pending',
            'message' => 'Quero adotar o Thor',
        ]);
        $requestRepository->saveFixture([
            'id' => 2,
            'user_id' => 3,
            'pet_id' => $petIdB,
            'status' => 'pending',
            'message' => 'Quero adotar a Luna',
        ]);

        $sessionManager = new SessionManager();
        $sessionManager->setUserId($orgAdminId);
        $sessionManager->setUserRole('organization_admin');

        $useCase = new ListOrganizationAdoptionRequests(
            $requestRepository,
            $userRepository,
            $sessionManager,
        );

        $result = $useCase->execute();

        self::assertCount(1, $result);
        self::assertSame($petIdA, $result[0]['pet_id']);
        self::assertSame('Thor', $result[0]['pet_name']);
        self::assertSame('pending', $result[0]['status']);
    }
}
