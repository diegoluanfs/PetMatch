<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Organization;

use InvalidArgumentException;
use PetMatch\Application\Organization\CreateOrganization;
use PetMatch\Application\Organization\EmailAlreadyRegisteredException;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryOrganizationRepository;

final class CreateOrganizationTest extends TestCase
{
    public function test_creates_an_organization_with_pending_status(): void
    {
        $repository = new InMemoryOrganizationRepository();
        $useCase = new CreateOrganization($repository);

        $result = $useCase->execute(
            'ONG PetMatch',
            'Organização parceira',
            'ong@example.com',
            '11999999999'
        );

        self::assertSame(1, $result['id']);
        self::assertSame('ONG PetMatch', $result['name']);
        self::assertSame('ong@example.com', $result['email']);
        self::assertSame('11999999999', $result['phone']);
        self::assertSame('pending', $result['status']);
    }

    public function test_rejects_duplicate_email(): void
    {
        $repository = new InMemoryOrganizationRepository();
        $useCase = new CreateOrganization($repository);
        $useCase->execute('ONG PetMatch', 'Organização parceira', 'ong@example.com', '11999999999');

        $this->expectException(EmailAlreadyRegisteredException::class);

        $useCase->execute('Outra ONG', 'Outra descrição', 'ong@example.com', '11888888888');
    }

    public function test_rejects_invalid_input(): void
    {
        $repository = new InMemoryOrganizationRepository();
        $useCase = new CreateOrganization($repository);

        $this->expectException(InvalidArgumentException::class);

        $useCase->execute('', '', 'invalid', '123');
    }
}
