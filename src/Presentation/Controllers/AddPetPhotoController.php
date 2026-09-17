<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\AddPetPhoto;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Infrastructure\Storage\LocalPetPhotoStorage;
use PetMatch\Presentation\Requests\Pet\AddPetPhotoRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class AddPetPhotoController
{
    public function __construct(
        private readonly AddPetPhoto $addPetPhoto,
        private readonly LocalPetPhotoStorage $photoStorage,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);
            $input = AddPetPhotoRequest::fromRequest($request);
            $uploadedFile = $request->file('photo');

            if ($uploadedFile !== null) {
                $input = new AddPetPhotoRequest(array_merge(
                    $input->input,
                    ['path' => $this->photoStorage->store($uploadedFile)],
                ));
            }

            return JsonResponse::created([
                'data' => $this->addPetPhoto->execute($petId, $input->input),
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
