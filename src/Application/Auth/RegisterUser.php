<?php

declare(strict_types=1);

namespace PetMatch\Application\Auth;

use InvalidArgumentException;
use PetMatch\Domain\User\User;
use PetMatch\Domain\User\UserRepository;

final class RegisterUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @return array{id: int, name: string, email: string, role: string, status: string}
     */
    public function execute(string $name, string $email, string $password): array
    {
        $name = trim($name);
        $email = mb_strtolower(trim($email));
        $password = trim($password);

        if ($name === '' || $email === '' || $password === '') {
            throw new InvalidArgumentException('Name, email and password are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException('Password must contain at least 8 characters.');
        }

        if ($this->userRepository->findByEmail($email) !== null) {
            throw new EmailAlreadyRegisteredException('Email already registered.');
        }

        $user = new User(
            null,
            null,
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            'adopter',
            'pending',
        );

        $id = $this->userRepository->save($user);

        return [
            'id' => $id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }
}
