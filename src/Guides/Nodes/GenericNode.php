<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Nodes;

final class GenericNode extends Node
{
    /** @var string */
    private $name;

    /**
     * @param Node|callable|string|null $value
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;

        parent::__construct($value);
    }

    public function getName() : string
    {
        return $this->name;
    }
}
