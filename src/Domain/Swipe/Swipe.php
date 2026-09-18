<?php

declare(strict_types=1);

namespace PetMatch\Domain\Swipe;

final class Swipe
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly int $petId,
        public readonly string $action,
    ) {
    }
}
