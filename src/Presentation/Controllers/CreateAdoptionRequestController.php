<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Adoption\AdoptionRequestAlreadyExistsException;
use PetMatch\Application\Adoption\CreateAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class CreateAdoptionRequestController
{
    public function __construct(
        private readonly CreateAdoptionRequest $createAdoptionRequest,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = $request->json();
            $adoptionRequest = $this->createAdoptionRequest->execute($payload);

            return JsonResponse::created([
                'data' => $adoptionRequest,
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (AdoptionRequestAlreadyExistsException $exception) {
            return JsonResponse::conflict([
                'error' => $exception->getMessage(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::unprocessableEntity([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
