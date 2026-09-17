<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Http;

final class ApplicationKernel
{
    public function handle(string $path): array
    {
        if ($path === '/' || $path === '/health') {
            return [
                'status' => 200,
                'body' => [
                    'name' => 'PetMatch',
                    'status' => 'ok',
                ],
            ];
        }

        return [
            'status' => 404,
            'body' => [
                'error' => 'Not Found',
            ],
        ];
    }
}
