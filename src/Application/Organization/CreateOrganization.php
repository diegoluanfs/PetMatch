<?php

declare(strict_types=1);

namespace PetMatch\Application\Organization;

use InvalidArgumentException;
use PetMatch\Domain\Organization\Organization;
use PetMatch\Domain\Organization\OrganizationRepository;

final class CreateOrganization
{
    public function __construct(
        private readonly OrganizationRepository $organizationRepository,
    ) {
    }

    /**
     * @return array{id: int, name: string, email: string, phone: string, status: string}
     */
    public function execute(string $name, string $description, string $email, string $phone): array
    {
        $name = trim($name);
        $description = trim($description);
        $email = mb_strtolower(trim($email));
        $phone = trim($phone);

        if ($name === '' || $email === '' || $phone === '') {
            throw new InvalidArgumentException('Name, email and phone are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        if (mb_strlen($phone) < 8) {
            throw new InvalidArgumentException('Phone must contain at least 8 characters.');
        }

        if ($this->organizationRepository->findByEmail($email) !== null) {
            throw new EmailAlreadyRegisteredException('Organization email already registered.');
        }

        $organization = new Organization(
            null,
            $name,
            $description === '' ? null : $description,
            $email,
            $phone,
            'pending',
        );

        $id = $this->organizationRepository->save($organization);

        return [
            'id' => $id,
            'name' => $organization->name,
            'email' => $organization->email,
            'phone' => $organization->phone,
            'status' => $organization->status,
        ];
    }
}
