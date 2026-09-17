<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Organization\CreateOrganization;
use PetMatch\Application\Organization\EmailAlreadyRegisteredException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Requests\Organization\CreateOrganizationRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class CreateOrganizationController
{
    public function __construct(
        private readonly CreateOrganization $createOrganization,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input = CreateOrganizationRequest::fromRequest($request);
            $organization = $this->createOrganization->execute(
                $input->name,
                $input->description,
                $input->email,
                $input->phone,
            );

            return JsonResponse::created([
                'data' => $organization,
            ]);
        } catch (EmailAlreadyRegisteredException $exception) {
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
