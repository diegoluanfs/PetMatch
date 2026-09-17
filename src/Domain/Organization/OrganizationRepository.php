<?php

declare(strict_types=1);

namespace PetMatch\Domain\Organization;

interface OrganizationRepository
{
    public function findByEmail(string $email): ?Organization;

    public function save(Organization $organization): int;
}
