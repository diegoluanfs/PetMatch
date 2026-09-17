<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Storage;

use InvalidArgumentException;
use RuntimeException;

final class LocalPetPhotoStorage
{
    public function __construct(
        private readonly string $storageDirectory,
    ) {
    }

    /**
     * @param array<string, mixed> $file
     */
    public function store(array $file): string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Invalid uploaded file.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? 'photo');
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $extension = $extension === '' ? 'jpg' : strtolower($extension);

        if ($tmpName === '' || !is_file($tmpName)) {
            throw new InvalidArgumentException('Uploaded file is missing.');
        }

        if (!is_dir($this->storageDirectory) && !mkdir($concurrentDirectory = $this->storageDirectory, 0775, true) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException('Unable to create photo storage directory.');
        }

        $fileName = sprintf('%s.%s', bin2hex(random_bytes(8)), $extension);
        $destination = rtrim($this->storageDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            if (!copy($tmpName, $destination)) {
                throw new RuntimeException('Unable to store uploaded photo.');
            }
        }

        return '/storage/pet-photos/' . $fileName;
    }
}
