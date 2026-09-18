<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Engagement;

use InvalidArgumentException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\SwipePet;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemorySwipeRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class SwipePetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_authenticated_user_can_like_a_pet(): void
    {
        $users = new InMemoryUserRepository();
        $userId = $users->save(new User(null, null, 'Maria', 'maria@example.com', 'hash', 'adopter', 'pending'));
        $session = new SessionManager();
        $session->setUserId($userId);

        $swipes = new InMemorySwipeRepository();
        $useCase = new SwipePet(new InMemoryPetRepository(), $swipes, $session);

        $result = $useCase->execute(1, 'like');

        self::assertSame('like', $result['action']);
        self::assertSame($userId, $result['user_id']);
        self::assertSame(1, $swipes->swipes[1]->petId);
    }

    public function test_rejects_unauthenticated_interaction(): void
    {
        $useCase = new SwipePet(new InMemoryPetRepository(), new InMemorySwipeRepository(), new SessionManager());

        $this->expectException(NotAuthenticatedException::class);
        $useCase->execute(1, 'like');
    }

    public function test_rejects_unknown_action(): void
    {
        $session = new SessionManager();
        $session->setUserId(1);
        $useCase = new SwipePet(new InMemoryPetRepository(), new InMemorySwipeRepository(), $session);

        $this->expectException(InvalidArgumentException::class);
        $useCase->execute(1, 'maybe');
    }
}
