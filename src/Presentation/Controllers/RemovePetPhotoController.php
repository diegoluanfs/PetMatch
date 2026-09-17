<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Application\Pet\PetPhotoNotFoundException;
use PetMatch\Application\Pet\RemovePetPhoto;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class RemovePetPhotoController
{
    public function __construct(
        private readonly RemovePetPhoto $removePetPhoto,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);
            $photoId = (int) ($request->parameter('photoId') ?? 0);

            return JsonResponse::ok([
                'data' => $this->removePetPhoto->execute($petId, $photoId),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (PetNotFoundException | PetPhotoNotFoundException $exception) {
            return JsonResponse::notFoundWithMessage($exception->getMessage());
        }
    }
}
