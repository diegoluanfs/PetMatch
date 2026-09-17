<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Pet\GetPet;
use PetMatch\Domain\Pet\PetPhoto;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetPhotoRepository;
use PetMatch\Tests\Support\InMemoryPetRepository;
use RuntimeException;

final class GetPetTest extends TestCase
{
    public function test_returns_a_pet(): void
    {
        $petPhotoRepository = new InMemoryPetPhotoRepository();
        $petPhotoRepository->save(new PetPhoto(null, 1, '/storage/pets/thor-1.jpg', 0));

        $useCase = new GetPet(new InMemoryPetRepository(), $petPhotoRepository);

        $result = $useCase->execute(1);

        self::assertSame(1, $result['id']);
        self::assertSame('Thor', $result['name']);
        self::assertCount(1, $result['photos']);
        self::assertSame('/storage/pets/thor-1.jpg', $result['photos'][0]['path']);
    }

    public function test_throws_when_pet_is_missing(): void
    {
        $useCase = new GetPet(new InMemoryPetRepository(), new InMemoryPetPhotoRepository());

        $this->expectException(RuntimeException::class);

        $useCase->execute(999);
    }
}
