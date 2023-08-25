<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;

abstract class Extension extends BaseExtension
{
    /**
     * The constructor of extensions should not be used.
     *
     * Extensions are loaded by the {@see ExtensionHandler}. An extension should not apply any logic in its
     */
    final public function __construct()
    {
    }
}
