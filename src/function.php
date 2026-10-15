<?php

declare(strict_types=1);

function loader()
{
    static $loader = null;

    if ($loader === null) {
        $loader = new \Loader\Loader();
    }

    return $loader;
}
