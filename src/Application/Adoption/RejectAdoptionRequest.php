<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class RejectAdoptionRequest
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
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
            throw new ForbiddenException('Only organization users can reject adoption requests.');
        }

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
            throw new ForbiddenException('Only pending requests can be rejected.');
        }

        $updatedRequest = new AdoptionRequest(
            $request->id,
            $request->userId,
            $request->petId,
            'rejected',
            $request->message,
        );

        $this->adoptionRequestRepository->update($updatedRequest);

        return [
            'id' => $updatedRequest->id,
            'user_id' => $updatedRequest->userId,
            'pet_id' => $updatedRequest->petId,
            'status' => $updatedRequest->status,
            'message' => $updatedRequest->message,
        ];
    }
}
