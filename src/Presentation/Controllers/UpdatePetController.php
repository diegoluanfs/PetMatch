<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Application\Pet\UpdatePet;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Requests\Pet\UpdatePetRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class UpdatePetController
{
    public function __construct(
        private readonly UpdatePet $updatePet,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);
            $input = UpdatePetRequest::fromRequest($request);

            return JsonResponse::ok([
                'data' => $this->updatePet->execute($petId, $input->input),
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
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::unprocessableEntity([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
