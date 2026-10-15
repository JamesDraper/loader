<?php

declare(strict_types=1);

namespace Loader\Internal;

use Loader\Resolvers\ResolverInterface;
use Loader\Resolvers\ClassResolver;
use Loader\Resolvers\Psr4Resolver;

/**
 * @internal
 */
class Loader
{
    private readonly LoaderHelper $helper;

    /**
     * @var list<ResolverInterface>
     */
    private array $resolvers = [];

    public function __construct(LoaderHelper $helper)
    {
        $this->helper = $helper;
    }

    public function autoload(string $class): void
    {
        foreach ($this->resolvers as $resolver) {
            $file = $resolver->resolve($class);

            if (!is_null($file)) {
                $this->helper->includeFile($file);
            }
        }
    }

    public function registerClassDirectory(string $namespace, string $directory): void
    {
        $this->registerResolver(new Psr4Resolver($namespace, $directory));
    }

    public function registerClassFile(string $class, string $file): void
    {
        $this->registerResolver(new ClassResolver($class, $file));
    }

    public function registerResolver(ResolverInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }
}
