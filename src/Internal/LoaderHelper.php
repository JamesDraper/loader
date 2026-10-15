<?php

declare(strict_types=1);

namespace Loader\Internal;

/**
 * @internal
 */
class LoaderHelper
{
    public function includeFile(string $file): void
    {
        @include_once $file;
    }
}
