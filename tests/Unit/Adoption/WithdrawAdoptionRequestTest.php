<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Adoption;

use PetMatch\Application\Adoption\WithdrawAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryAdoptionRequestRepository;
use PHPUnit\Framework\TestCase;

final class WithdrawAdoptionRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_withdraws_own_pending_request(): void
    {
        $repository = new InMemoryAdoptionRequestRepository();
        $repository->saveFixture(['id' => 1, 'user_id' => 7, 'pet_id' => 2, 'status' => 'pending', 'message' => 'Ainda tenho interesse']);
        $session = new SessionManager();
        $session->setUserId(7);

        $useCase = new WithdrawAdoptionRequest($repository, $session);
        $result = $useCase->execute(1);

        self::assertSame('withdrawn', $result['status']);
        self::assertSame('withdrawn', $repository->findById(1)->status);
    }

    public function test_rejects_other_user_request(): void
    {
        $repository = new InMemoryAdoptionRequestRepository();
        $repository->saveFixture(['id' => 1, 'user_id' => 8, 'pet_id' => 2, 'status' => 'pending', 'message' => null]);
        $session = new SessionManager();
        $session->setUserId(7);

        $this->expectException(ForbiddenException::class);
        $useCase = new WithdrawAdoptionRequest($repository, $session);
        $useCase->execute(1);
    }

    public function test_rejects_approved_request(): void
    {
        $repository = new InMemoryAdoptionRequestRepository();
        $repository->saveFixture(['id' => 1, 'user_id' => 7, 'pet_id' => 2, 'status' => 'approved', 'message' => null]);
        $session = new SessionManager();
        $session->setUserId(7);

        $this->expectException(ForbiddenException::class);
        $useCase = new WithdrawAdoptionRequest($repository, $session);
        $useCase->execute(1);
    }

    public function test_requires_authentication(): void
    {
        $this->expectException(NotAuthenticatedException::class);
        $useCase = new WithdrawAdoptionRequest(new InMemoryAdoptionRequestRepository(), new SessionManager());
        $useCase->execute(1);
    }
}
