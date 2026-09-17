<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\User\User;
use PetMatch\Domain\User\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /**
     * @var array<int, User>
     */
    private array $users = [];

    private int $nextId = 1;

    public function findById(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if (mb_strtolower($user->email) === mb_strtolower($email)) {
                return $user;
            }
        }

        return null;
    }

    public function save(User $user): int
    {
        $id = $this->nextId++;
        $this->users[$id] = new User(
            $id,
            $user->organizationId,
            $user->name,
            $user->email,
            $user->passwordHash,
            $user->role,
            $user->status,
        );

        return $id;
    }
}
