<?php

declare(strict_types=1);

namespace Loader;

use Loader\Internal\LoaderHelper as InternalLoaderHelper;
use Loader\Internal\Loader as InternalLoader;
use Loader\Resolvers\ResolverInterface;

final class Loader
{
    private readonly InternalLoader $internalLoader;

    public function __construct()
    {
        $this->internalLoader = new InternalLoader(
            new InternalLoaderHelper(),
        );

        spl_autoload_register(fn($class) => $this->internalLoader->autoload($class));
    }

    public function registerClassDirectory(string $namespace, string $directory): self
    {
        $this->internalLoader->registerClassDirectory($namespace, $directory);

        return $this;
    }

    public function registerClassFile(string $class, string $file): self
    {
        $this->internalLoader->registerClassFile($class, $file);

        return $this;
    }

    public function registerResolver(ResolverInterface $resolver): self
    {
        $this->internalLoader->registerResolver($resolver);

        return $this;
    }
}
