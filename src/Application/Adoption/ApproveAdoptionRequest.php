<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Transaction\TransactionManager;
use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Domain\Pet\Pet;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class ApproveAdoptionRequest
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
        private readonly ?TransactionManager $transactionManager = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $requestId): array
    {
        $currentUserId = $this->sessionManager->userId();

        if ($currentUserId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $currentUser = $this->userRepository->findById($currentUserId);

        if ($currentUser === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        if (!in_array($currentUser->role, ['organization_admin', 'admin'], true) || $currentUser->organizationId === null) {
            throw new ForbiddenException('Only organization users can approve adoption requests.');
        }

        $this->transactionManager?->begin();

        try {
            $request = $this->adoptionRequestRepository->findById($requestId);

            if ($request === null) {
                throw new AdoptionRequestNotFoundException('Adoption request not found.');
            }

            $pet = $this->petRepository->findById($request->petId);

            if ($pet === null) {
                throw new AdoptionRequestNotFoundException('Pet not found.');
            }

            if ($pet->organizationId !== $currentUser->organizationId) {
                throw new ForbiddenException('You can only manage requests for your own pets.');
            }

            if ($request->status !== 'pending') {
                throw new ForbiddenException('Only pending requests can be approved.');
            }

            $updatedRequest = new AdoptionRequest(
                $request->id,
                $request->userId,
                $request->petId,
                'approved',
                $request->message,
            );

            $this->adoptionRequestRepository->update($updatedRequest);

            $adoptedPet = new Pet(
                $pet->id,
                $pet->organizationId,
                $pet->name,
                $pet->description,
                $pet->animalType,
                $pet->breed,
                $pet->gender,
                $pet->birthDate,
                $pet->size,
                'adopted',
                $pet->city,
                $pet->state,
                $pet->latitude,
                $pet->longitude,
            );

            $this->petRepository->update($adoptedPet);

            $this->transactionManager?->commit();

            return [
                'id' => $updatedRequest->id,
                'user_id' => $updatedRequest->userId,
                'pet_id' => $updatedRequest->petId,
                'status' => $updatedRequest->status,
                'message' => $updatedRequest->message,
            ];
        } catch (\Throwable $exception) {
            $this->transactionManager?->rollback();
            throw $exception;
        }
    }
}
