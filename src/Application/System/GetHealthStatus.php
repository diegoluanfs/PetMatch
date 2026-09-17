<?php

declare(strict_types=1);

namespace PetMatch\Application\System;

final class GetHealthStatus
{
    public function execute(): array
    {
        return [
            'name' => 'PetMatch',
            'status' => 'ok',
        ];
    }
}
