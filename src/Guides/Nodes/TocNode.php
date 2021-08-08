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

class TocNode extends Node
{
    private const DEFAULT_DEPTH = 2;

    /** @var string[] */
    protected $files;

    /**
     * @param string[] $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function getDepth(): int
    {
        if ($this->getOption('depth')) {
            return (int) $this->getOption('depth');
        }

        if ($this->getOption('maxdepth')) {
            return (int) $this->getOption('maxdepth');
        }

        return self::DEFAULT_DEPTH;
    }
}
