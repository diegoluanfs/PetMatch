<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Pet\GetPet;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;
use RuntimeException;

final class GetPetTest extends TestCase
{
    public function test_returns_a_pet(): void
    {
        $useCase = new GetPet(new InMemoryPetRepository());

        $result = $useCase->execute(1);

        self::assertSame(1, $result['id']);
        self::assertSame('Thor', $result['name']);
    }

    public function test_throws_when_pet_is_missing(): void
    {
        $useCase = new GetPet(new InMemoryPetRepository());

        $this->expectException(RuntimeException::class);

        $useCase->execute(999);
    }
}
