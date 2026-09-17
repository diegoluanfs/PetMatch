<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\ArchivePet;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ArchivePetController
{
    public function __construct(
        private readonly ArchivePet $archivePet,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);

            return JsonResponse::ok([
                'data' => $this->archivePet->execute($petId),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (PetNotFoundException $exception) {
            return JsonResponse::notFoundWithMessage($exception->getMessage());
        }
    }
}
