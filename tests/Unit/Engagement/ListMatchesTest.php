<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\ListMatches;
use PetMatch\Domain\Swipe\Swipe;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemorySwipeRepository;
use PHPUnit\Framework\TestCase;

final class ListMatchesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_lists_available_liked_pets_as_matches(): void
    {
        $repository = new InMemorySwipeRepository();
        $repository->save(new Swipe(null, 7, 1, 'like'));
        $repository->save(new Swipe(null, 7, 2, 'pass'));
        $session = new SessionManager();
        $session->setUserId(7);

        $useCase = new ListMatches($repository, $session);
        $result = $useCase->execute();

        self::assertCount(1, $result);
        self::assertSame(1, $result[0]['pet_id']);
    }

    public function test_requires_authentication(): void
    {
        $this->expectException(NotAuthenticatedException::class);
        $useCase = new ListMatches(new InMemorySwipeRepository(), new SessionManager());
        $useCase->execute();
    }
}
