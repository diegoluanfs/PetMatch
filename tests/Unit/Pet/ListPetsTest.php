<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Pet\ListPets;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;

final class ListPetsTest extends TestCase
{
    public function test_lists_pets(): void
    {
        $useCase = new ListPets(new InMemoryPetRepository());

        $result = $useCase->execute();

        self::assertCount(2, $result);
        self::assertSame('Thor', $result[0]['name']);
        self::assertSame('Luna', $result[1]['name']);
    }
}
