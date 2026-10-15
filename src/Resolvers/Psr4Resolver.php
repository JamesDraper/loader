<?php

declare(strict_types=1);

namespace Loader\Resolvers;

use function str_starts_with;
use function str_replace;
use function strlen;
use function substr;

final class Psr4Resolver implements ResolverInterface
{
    private readonly string $namespace;

    private readonly string $directory;

    public function __construct(string $namespace, string $directory)
    {
        if (!str_ends_with($namespace, '\\')) {
            $namespace .= '\\';
        }

        if (!str_ends_with($directory, '/')) {
            $directory .= '/';
        }

        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    public function resolve(string $class): ?string
    {
        $prefix = $this->namespace;

        if (!str_starts_with($class, $prefix)) {
            return null;
        }

        $relative = substr($class, strlen($prefix));

        if ('' === $relative) {
            return null;
        }

        return $this->directory
            . str_replace('\\', '/', $relative)
            . '.php';
    }
}
