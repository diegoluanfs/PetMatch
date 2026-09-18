<?php

declare(strict_types=1);

namespace PetMatch\Domain\Adoption;

interface AdoptionRequestRepository
{
    public function findById(int $id): ?AdoptionRequest;

    public function findActiveByUserAndPet(int $userId, int $petId): ?array;

    /**
     * @return list<array<string, mixed>>
     */
    public function findByUserId(int $userId): array;

    /**
     * @return list<array<string, mixed>>
     */
    public function findForOrganization(int $organizationId): array;

    public function save(AdoptionRequest $request): int;

    public function update(AdoptionRequest $request): void;
}
