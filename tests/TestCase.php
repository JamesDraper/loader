<?php

declare(strict_types=1);

namespace Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Mockery;

abstract class TestCase extends MockeryTestCase
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T&MockInterface
     */
    final protected function makeMock(string $class): MockInterface
    {
        return Mockery::mock($class);
    }
}
