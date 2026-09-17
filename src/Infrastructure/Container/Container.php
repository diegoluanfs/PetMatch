<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Container;

use LogicException;

final class Container
{
    /**
     * @var array<string, callable(self): mixed>
     */
    private array $definitions = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    /**
     * @param callable(self): mixed $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->definitions[$id] = $factory;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->instances);
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->definitions)) {
            throw new LogicException(sprintf('Service "%s" is not registered.', $id));
        }

        $this->instances[$id] = ($this->definitions[$id])($this);

        return $this->instances[$id];
    }
}
