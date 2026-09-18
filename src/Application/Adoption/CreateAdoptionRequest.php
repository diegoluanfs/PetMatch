<?php

declare(strict_types=1);

namespace PetMatch\Application\Adoption;

use InvalidArgumentException;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Domain\Pet\PetRepository;
use PetMatch\Domain\User\UserRepository;
use PetMatch\Domain\User\UserVerificationRepository;
use PetMatch\Infrastructure\Security\SessionManager;

final class CreateAdoptionRequest
{
    public function __construct(
        private readonly PetRepository $petRepository,
        private readonly AdoptionRequestRepository $adoptionRequestRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionManager $sessionManager,
        private readonly ?UserVerificationRepository $userVerificationRepository = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $currentUserId = $this->sessionManager->userId();

        if ($currentUserId === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        $currentUser = $this->userRepository->findById($currentUserId);

        if ($currentUser === null) {
            throw new NotAuthenticatedException('Authentication required.');
        }

        if ($currentUser->role !== 'adopter') {
            throw new ForbiddenException('Only adopters can create adoption requests.');
        }

        if ($this->userVerificationRepository !== null
            && (!$this->userVerificationRepository->isVerified($currentUserId, 'email')
                || !$this->userVerificationRepository->isVerified($currentUserId, 'whatsapp'))
        ) {
            throw new ForbiddenException('Email and WhatsApp verification are required to request adoption.');
        }

        $petId = (int) ($input['pet_id'] ?? 0);
        $message = trim((string) ($input['message'] ?? ''));

        if ($petId <= 0) {
            throw new InvalidArgumentException('Pet id is required.');
        }

        $pet = $this->petRepository->findById($petId);

        if ($pet === null) {
            throw new InvalidArgumentException('Pet not found.');
        }

        if ($pet->status !== 'available') {
            throw new ForbiddenException('Only available pets can receive adoption requests.');
        }

        if ($this->adoptionRequestRepository->findActiveByUserAndPet($currentUserId, $petId) !== null) {
            throw new AdoptionRequestAlreadyExistsException('A pending or approved adoption request already exists for this pet.');
        }

        $request = new AdoptionRequest(
            null,
            $currentUserId,
            $petId,
            'pending',
            $message === '' ? null : $message,
        );

        $id = $this->adoptionRequestRepository->save($request);

        return [
            'id' => $id,
            'user_id' => $currentUserId,
            'pet_id' => $petId,
            'status' => 'pending',
            'message' => $message === '' ? null : $message,
        ];
    }
}
