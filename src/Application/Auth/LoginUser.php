<?php

declare(strict_types=1);

namespace PetMatch\Application\Auth;

use PetMatch\Domain\User\UserRepository;

final class LoginUser
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @return array{id: int, name: string, email: string, role: string, status: string}
     */
    public function execute(string $email, string $password): array
    {
        $email = mb_strtolower(trim($email));
        $password = trim($password);

        if ($email === '' || $password === '') {
            throw new InvalidCredentialsException('Invalid credentials.');
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !password_verify($password, $user->passwordHash)) {
            throw new InvalidCredentialsException('Invalid credentials.');
        }

        return [
            'id' => $user->id ?? 0,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }
}
