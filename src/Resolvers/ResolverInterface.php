<?php

declare(strict_types=1);

namespace Loader\Resolvers;

interface ResolverInterface
{
    public function resolve(string $class): ?string;
}
