<?php

declare(strict_types=1);

namespace Tests\Resolvers;

use Loader\Resolvers\ResolverInterface;
use Loader\Resolvers\ClassResolver;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use function is_subclass_of;

/**
 * @phpstan-type Scenario array{
 *     mappedClass: string,
 *     file: string,
 *     class: string,
 *     expected: ?string,
 * }
 */
final class ClassResolverTest extends TestCase
{
    #[Test]
    public function it_implements_the_resolver_interface(): void
    {
        $this->assertTrue(is_subclass_of(ClassResolver::class, ResolverInterface::class));
    }

    #[Test]
    #[DataProvider('it_resolves_classes_provider')]
    public function it_resolves_classes(string $mappedClass, string $file, string $class, ?string $expected): void
    {
        $resolver = new ClassResolver($mappedClass, $file);

        $this->assertSame($expected, $resolver->resolve($class));
    }

    /**
     * @return array<string, Scenario>
     */
    public static function it_resolves_classes_provider(): array
    {
        return [
            'the mapped class' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => 'Some\\Namespace\\Class',
                'expected' => 'some/directory/file.php',
            ],
            'the mapped class in the global namespace' => [
                'mappedClass' => 'Class',
                'file' => 'some/directory/file.php',
                'class' => 'Class',
                'expected' => 'some/directory/file.php',
            ],
            'a class sharing the prefix of the mapped class' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => 'Some\\Namespace\\OtherClass',
                'expected' => null,
            ],
            'a class in a sub-namespace of the mapped class' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => 'Some\\Namespace\\Class\\Child',
                'expected' => null,
            ],
            'the namespace of the mapped class' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => 'Some\\Namespace',
                'expected' => null,
            ],
            'the mapped class in a different case' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => 'some\\namespace\\class',
                'expected' => null,
            ],
            'empty class name' => [
                'mappedClass' => 'Some\\Namespace\\Class',
                'file' => 'some/directory/file.php',
                'class' => '',
                'expected' => null,
            ],
        ];
    }
}
