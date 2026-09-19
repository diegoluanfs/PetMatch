<?php

declare(strict_types=1);

namespace PetMatch\Domain\Engagement;

final class Favorite
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly int $petId,
    ) {
    }
}
