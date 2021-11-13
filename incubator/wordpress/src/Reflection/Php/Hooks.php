<?php

declare(strict_types=1);

namespace phpDocumentor\Wordpress\Reflection\Php;

use phpDocumentor\Reflection\Metadata\Metadata;

final class Hooks implements Metadata
{
    private $actions = [];

    public function key(): string
    {
        return 'wp_hooks';
    }

    public function addAction(Action $action): void
    {
        $this->actions[] = $action;
    }

    /** @return Action[] */
    public function getActions(): array
    {
        return $this->actions;
    }
}
