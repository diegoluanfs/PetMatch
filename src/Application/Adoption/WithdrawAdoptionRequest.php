<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class WithdrawAdoptionRequest
{
    public function __construct(
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $requestId): array
    {
        $userId = $this->sessionManager->userId();

        if ($userId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $request = $this->adoptionRequestRepository->findById($requestId);

        if ($request === null) {
            throw new AdoptionRequestNotFoundException('Adoption request not found.');
        }

        if ($request->userId !== $userId) {
            throw new ForbiddenException('You can only withdraw your own requests.');
        }

        if ($request->status !== 'pending') {
            throw new ForbiddenException('Only pending requests can be withdrawn.');
        }

        $withdrawnRequest = new AdoptionRequest(
            $request->id,
            $request->userId,
            $request->petId,
            'withdrawn',
            $request->message,
        );

        $this->adoptionRequestRepository->update($withdrawnRequest);

        return [
            'id' => $withdrawnRequest->id,
            'user_id' => $withdrawnRequest->userId,
            'pet_id' => $withdrawnRequest->petId,
            'status' => $withdrawnRequest->status,
            'message' => $withdrawnRequest->message,
        ];
    }
}
