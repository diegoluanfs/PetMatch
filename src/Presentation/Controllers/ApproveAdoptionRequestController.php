<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Adoption\AdoptionRequestNotFoundException;
use PetMatch\Application\Adoption\ApproveAdoptionRequest;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ApproveAdoptionRequestController
{
    public function __construct(
        private readonly ApproveAdoptionRequest $approveAdoptionRequest,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $requestId = (int) ($request->parameter('id') ?? 0);

            return JsonResponse::ok([
                'data' => $this->approveAdoptionRequest->execute($requestId),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (AdoptionRequestNotFoundException $exception) {
            return JsonResponse::notFoundWithMessage($exception->getMessage());
        }
    }
}
