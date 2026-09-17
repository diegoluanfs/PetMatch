<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Infrastructure\Http\Request;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Presentation\Responses\JsonResponse;

final class LogoutUserController
{
    public function __construct(
        private readonly SessionManager $sessionManager,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->sessionManager->clear();

        return JsonResponse::ok([
            'data' => [
                'message' => 'Logged out successfully.',
            ],
        ]);
    }
}
