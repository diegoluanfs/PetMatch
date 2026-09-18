<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Adoption\AdoptionRequest;
use PetMatch\Domain\Adoption\AdoptionRequestRepository;
use PetMatch\Domain\Pet\PetRepository;

final class InMemoryAdoptionRequestRepository implements AdoptionRequestRepository
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $requests = [];

    private ?PetRepository $petRepository = null;

    private int $nextId = 1;

    public function findById(int $id): ?AdoptionRequest
    {
        $request = $this->requests[$id] ?? null;

        if ($request === null) {
            return null;
        }

        return new AdoptionRequest(
            (int) $request['id'],
            (int) $request['user_id'],
            (int) $request['pet_id'],
            (string) $request['status'],
            $request['message'] !== null ? (string) $request['message'] : null,
        );
    }

    public function findActiveByUserAndPet(int $userId, int $petId): ?array
    {
        foreach ($this->requests as $request) {
            if ((int) $request['user_id'] === $userId && (int) $request['pet_id'] === $petId && in_array($request['status'], ['pending', 'approved'], true)) {
                return $request;
            }
        }

        return null;
    }

    public function findByUserId(int $userId): array
    {
        return array_values(array_filter(
            $this->requests,
            static fn (array $request): bool => (int) $request['user_id'] === $userId
        ));
    }

    public function findForOrganization(int $organizationId): array
    {
        if ($this->petRepository === null) {
            return [];
        }

        $requests = [];

        foreach ($this->requests as $request) {
            $pet = $this->petRepository->findById((int) $request['pet_id']);
            if ($pet !== null) {
                if ($pet->organizationId === $organizationId) {
                    $requests[] = [
                        'id' => (int) $request['id'],
                        'user_id' => (int) $request['user_id'],
                        'pet_id' => (int) $request['pet_id'],
                        'pet_name' => $pet->name,
                        'status' => $request['status'],
                        'message' => $request['message'] ?? null,
                    ];
                }
            }
        }

        return $requests;
    }

    public function setPetRepository(PetRepository $petRepository): void
    {
        $this->petRepository = $petRepository;
    }

    public function save(AdoptionRequest $request): int
    {
        $id = $this->nextId++;
        $this->requests[$id] = [
            'id' => $id,
            'user_id' => $request->userId,
            'pet_id' => $request->petId,
            'status' => $request->status,
            'message' => $request->message,
        ];

        return $id;
    }

    public function update(AdoptionRequest $request): void
    {
        if ($request->id === null) {
            return;
        }

        $this->requests[$request->id] = [
            'id' => $request->id,
            'user_id' => $request->userId,
            'pet_id' => $request->petId,
            'status' => $request->status,
            'message' => $request->message,
        ];
    }

    /**
     * @param array<string, mixed> $request
     */
    public function saveFixture(array $request): int
    {
        $id = $request['id'] ?? $this->nextId++;
        $this->requests[$id] = [
            'id' => $id,
            'user_id' => (int) $request['user_id'],
            'pet_id' => (int) $request['pet_id'],
            'status' => (string) $request['status'],
            'message' => $request['message'] ?? null,
        ];

        $this->nextId = max($this->nextId, $id + 1);

        return $id;
    }
}
