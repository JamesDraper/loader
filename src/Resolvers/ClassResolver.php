<?php

declare(strict_types=1);

namespace Loader\Resolvers;

final class ClassResolver implements ResolverInterface
{
    private readonly string $class;

    private readonly string $file;

    public function __construct(string $class, string $file)
    {
        $this->class = $class;
        $this->file = $file;
    }

    public function resolve(string $class): ?string
    {
        if ($class !== $this->class) {
            return null;
        }

        return $this->file;
    }
}
