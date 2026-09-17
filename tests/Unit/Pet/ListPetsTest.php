<?php

declare(strict_types=1);

namespace PetMatch\Tests\Unit\Pet;

use PetMatch\Application\Pet\ListPets;
use PetMatch\Domain\Pet\PetPhoto;
use PetMatch\Tests\Support\InMemoryPetPhotoRepository;
use PHPUnit\Framework\TestCase;
use PetMatch\Tests\Support\InMemoryPetRepository;

final class ListPetsTest extends TestCase
{
    public function test_lists_pets(): void
    {
        $petPhotoRepository = new InMemoryPetPhotoRepository();
        $petPhotoRepository->save(new PetPhoto(null, 1, '/storage/pets/thor-1.jpg', 0));

        $useCase = new ListPets(new InMemoryPetRepository(), $petPhotoRepository);

        $result = $useCase->execute();

        self::assertCount(2, $result);
        self::assertSame('Thor', $result[0]['name']);
        self::assertSame('Luna', $result[1]['name']);
        self::assertCount(1, $result[0]['photos']);
        self::assertSame('/storage/pets/thor-1.jpg', $result[0]['photos'][0]['path']);
    }

    public function test_excludes_archived_pets(): void
    {
        $repository = new InMemoryPetRepository();
        $repository->save(new \PetMatch\Domain\Pet\Pet(
            null,
            1,
            'Archived Pet',
            'Pet arquivado',
            'dog',
            'labrador',
            'male',
            '2022-01-01',
            'large',
            'archived',
            'Santa Maria',
            'RS',
            null,
            null,
        ));

        $useCase = new ListPets($repository, new InMemoryPetPhotoRepository());

        $result = $useCase->execute();

        self::assertCount(2, $result);
        self::assertSame('Thor', $result[0]['name']);
        self::assertSame('Luna', $result[1]['name']);
    }
}
