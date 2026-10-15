<?php

declare(strict_types=1);

namespace Tests\Internal;

use Loader\Resolvers\ResolverInterface;
use Loader\Internal\LoaderHelper;
use Loader\Internal\Loader;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;

use Tests\TestCase;

final class LoaderTest extends TestCase
{
    /**
     * @var MockInterface&LoaderHelper
     */
    private LoaderHelper $helper;

    private Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helper = $this->makeMock(LoaderHelper::class);
        $this->loader = new Loader($this->helper);
    }

    #[Test]
    public function it_does_nothing_when_no_resolvers_are_registered(): void
    {
        $this->helper->shouldNotReceive('includeFile');

        $this->loader->autoload('Some\\Namespace\\Class');
    }

    #[Test]
    public function it_includes_the_file_resolved_by_a_registered_resolver(): void
    {
        $resolver = $this->makeMock(ResolverInterface::class);
        
        $resolver->expects('resolve')->with('Some\\Namespace\\Class')->andReturn('some/directory/Class.php');

        $this->helper->expects('includeFile')->with('some/directory/Class.php');

        $this->loader->registerResolver($resolver);
        $this->loader->autoload('Some\\Namespace\\Class');
    }

    #[Test]
    public function it_does_not_include_anything_when_the_resolver_cannot_resolve_the_class(): void
    {
        $resolver = $this->makeMock(ResolverInterface::class);
        $resolver->expects('resolve')->with('Some\\Namespace\\Class')->andReturnNull();

        $this->helper->shouldNotReceive('includeFile');

        $this->loader->registerResolver($resolver);
        $this->loader->autoload('Some\\Namespace\\Class');
    }

    #[Test]
    public function it_includes_the_file_from_every_resolver_that_resolves_the_class_in_registration_order(): void
    {
        $first = $this->makeMock(ResolverInterface::class);
        $first->expects('resolve')->with('Some\\Namespace\\Class')->andReturn('first/Class.php');

        $second = $this->makeMock(ResolverInterface::class);
        $second->expects('resolve')->with('Some\\Namespace\\Class')->andReturnNull();

        $third = $this->makeMock(ResolverInterface::class);
        $third->expects('resolve')->with('Some\\Namespace\\Class')->andReturn('third/Class.php');

        $this->helper->expects('includeFile')->with('first/Class.php')->ordered();
        $this->helper->expects('includeFile')->with('third/Class.php')->ordered();

        $this->loader->registerResolver($first);
        $this->loader->registerResolver($second);
        $this->loader->registerResolver($third);
        $this->loader->autoload('Some\\Namespace\\Class');
    }

    #[Test]
    public function it_includes_the_file_of_a_registered_class_file(): void
    {
        $this->helper->expects('includeFile')->with('some/directory/file.php');

        $this->loader->registerClassFile('Some\\Namespace\\Class', 'some/directory/file.php');
        $this->loader->autoload('Some\\Namespace\\Class');
    }

    #[Test]
    public function it_does_not_include_a_registered_class_file_for_a_different_class(): void
    {
        $this->helper->shouldNotReceive('includeFile');

        $this->loader->registerClassFile('Some\\Namespace\\Class', 'some/directory/file.php');
        $this->loader->autoload('Some\\Namespace\\OtherClass');
    }

    #[Test]
    public function it_includes_a_file_from_a_registered_class_directory(): void
    {
        $this->helper->expects('includeFile')->with('some/directory/Sub/Class.php');

        $this->loader->registerClassDirectory('Some\\Namespace', 'some/directory');
        $this->loader->autoload('Some\\Namespace\\Sub\\Class');
    }

    #[Test]
    public function it_does_not_include_a_file_from_a_registered_class_directory_for_an_unrelated_class(): void
    {
        $this->helper->shouldNotReceive('includeFile');

        $this->loader->registerClassDirectory('Some\\Namespace', 'some/directory');
        $this->loader->autoload('Other\\Namespace\\Class');
    }

    #[Test]
    public function it_consults_class_files_class_directories_and_custom_resolvers_together(): void
    {
        $resolver = $this->makeMock(ResolverInterface::class);
        $resolver->expects('resolve')->with('Some\\Namespace\\Class')->andReturn('custom/Class.php');

        $this->helper->expects('includeFile')->with('mapped/file.php')->ordered();
        $this->helper->expects('includeFile')->with('some/directory/Class.php')->ordered();
        $this->helper->expects('includeFile')->with('custom/Class.php')->ordered();

        $this->loader->registerClassFile('Some\\Namespace\\Class', 'mapped/file.php');
        $this->loader->registerClassDirectory('Some\\Namespace', 'some/directory');
        $this->loader->registerResolver($resolver);
        $this->loader->autoload('Some\\Namespace\\Class');
    }
}
