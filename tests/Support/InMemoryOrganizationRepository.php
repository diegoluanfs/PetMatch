<?php

declare(strict_types=1);

namespace PetMatch\Tests\Support;

use PetMatch\Domain\Organization\Organization;
use PetMatch\Domain\Organization\OrganizationRepository;

final class InMemoryOrganizationRepository implements OrganizationRepository
{
    /**
     * @var array<int, Organization>
     */
    private array $organizations = [];

    private int $nextId = 1;

    public function findByEmail(string $email): ?Organization
    {
        foreach ($this->organizations as $organization) {
            if (mb_strtolower($organization->email) === mb_strtolower($email)) {
                return $organization;
            }
        }

        return null;
    }

    public function save(Organization $organization): int
    {
        $id = $this->nextId++;
        $this->organizations[$id] = new Organization(
            $id,
            $organization->name,
            $organization->description,
            $organization->email,
            $organization->phone,
            $organization->status,
        );

        return $id;
    }
}
