<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Adoption\AdoptionRequestNotFoundException;
use PetMatch\Application\Adoption\WithdrawAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class WithdrawAdoptionRequestController
{
    public function __construct(
        private readonly WithdrawAdoptionRequest $withdrawAdoptionRequest,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok([
                'data' => $this->withdrawAdoptionRequest->execute((int) ($request->parameter('id') ?? 0)),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden(['error' => $exception->getMessage()]);
        } catch (AdoptionRequestNotFoundException $exception) {
            return JsonResponse::notFoundWithMessage($exception->getMessage());
        }
    }
}
