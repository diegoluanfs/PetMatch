<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Engagement;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\CreateFavorite;
use PetMatch\Application\Engagement\ListFavorites;
use PetMatch\Application\Engagement\RemoveFavorite;
use PetMatch\Domain\User\User;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Tests\Support\InMemoryFavoriteRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use PetMatch\Tests\Support\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;

final class FavoriteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function test_creates_idempotent_favorite_and_lists_it(): void
    {
        $users = new InMemoryUserRepository();
        $userId = $users->save(new User(null, null, 'Maria', 'maria@example.com', 'hash', 'adopter', 'active'));
        $session = new SessionManager();
        $session->setUserId($userId);
        $favorites = new InMemoryFavoriteRepository();
        $create = new CreateFavorite(new InMemoryPetRepository(), $favorites, $session);

        $first = $create->execute(1);
        $second = $create->execute(1);
        $listFavorites = new ListFavorites($favorites, $session);
        $listed = $listFavorites->execute();

        self::assertSame($first['id'], $second['id']);
        self::assertCount(1, $listed);
    }

    public function test_removes_own_favorite(): void
    {
        $session = new SessionManager();
        $session->setUserId(7);
        $favorites = new InMemoryFavoriteRepository();
        $create = new CreateFavorite(new InMemoryPetRepository(), $favorites, $session);
        $create->execute(1);

        $removeFavorite = new RemoveFavorite($favorites, $session);
        $removeFavorite->execute(1);

        self::assertCount(0, $favorites->findByUserId(7));
    }

    public function test_requires_authentication(): void
    {
        $this->expectException(NotAuthenticatedException::class);
        $useCase = new ListFavorites(new InMemoryFavoriteRepository(), new SessionManager());
        $useCase->execute();
    }
}
