<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\CreatePet;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Requests\Pet\CreatePetRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class CreatePetController
{
    public function __construct(
        private readonly CreatePet $createPet,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input = CreatePetRequest::fromRequest($request);
            $pet = $this->createPet->execute($input->input);

            return JsonResponse::created([
                'data' => $pet,
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::unprocessableEntity([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
