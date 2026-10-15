<?php

declare(strict_types=1);

namespace Tests\Resolvers;

use Loader\Resolvers\ResolverInterface;
use Loader\Resolvers\Psr4Resolver;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function is_subclass_of;

/**
 * @phpstan-type Scenario array{
 *     namespace: string,
 *     directory: string,
 *     class: string,
 *     expected: ?string,
 * }
 */
final class Psr4ResolverTest extends TestCase
{
    #[Test]
    public function it_implements_the_resolver_interface(): void
    {
        $this->assertTrue(is_subclass_of(Psr4Resolver::class, ResolverInterface::class));
    }

    #[Test]
    #[DataProvider('it_resolves_classes_provider')]
    public function it_resolves_classes(string $namespace, string $directory, string $class, ?string $expected): void
    {
        $resolver = new Psr4Resolver($namespace, $directory);

        $this->assertSame($expected, $resolver->resolve($class));
    }

    /**
     * @return array<string, Scenario>
     */
    public static function it_resolves_classes_provider(): array
    {
        return [
            'class directly under the namespace' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace\\Class',
                'expected' => 'some/directory/Class.php',
            ],
            'class in a sub-namespace' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace\\Class\\Several\\Layers\\Deep',
                'expected' => 'some/directory/Class/Several/Layers/Deep.php',
            ],
            'class in an unrelated namespace' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Other\\Namespace\\Class',
                'expected' => null,
            ],
            'class in the parent namespace' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Class',
                'expected' => null,
            ],
            'class in a namespace sharing the prefix without a separator' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespaces\\Class',
                'expected' => null,
            ],
            'namespace with a trailing separator' => [
                'namespace' => 'Some\\Namespace\\',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace\\Class',
                'expected' => 'some/directory/Class.php',
            ],
            'directory with a trailing separator' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory/',
                'class' => 'Some\\Namespace\\Class',
                'expected' => 'some/directory/Class.php',
            ],
            'namespace and directory with trailing separators' => [
                'namespace' => 'Some\\Namespace\\',
                'directory' => 'some/directory/',
                'class' => 'Some\\Namespace\\Class',
                'expected' => 'some/directory/Class.php',
            ],
            'single-segment namespace' => [
                'namespace' => 'Some',
                'directory' => 'some/directory',
                'class' => 'Some\\Class',
                'expected' => 'some/directory/Class.php',
            ],
            'class name case is preserved' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace\\SomeClass',
                'expected' => 'some/directory/SomeClass.php',
            ],
            'namespace itself' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace',
                'expected' => null,
            ],
            'namespace itself with a trailing separator' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Some\\Namespace\\',
                'expected' => null,
            ],
            'namespace in a different case' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'some\\namespace\\Class',
                'expected' => null,
            ],
            'namespace as a later segment of the class' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => 'Other\\Some\\Namespace\\Class',
                'expected' => null,
            ],
            'empty class name' => [
                'namespace' => 'Some\\Namespace',
                'directory' => 'some/directory',
                'class' => '',
                'expected' => null,
            ],
        ];
    }
}
