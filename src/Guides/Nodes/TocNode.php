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

use phpDocumentor\Guides\Environment;

class TocNode extends Node
{
    private const DEFAULT_DEPTH = 2;

    /** @var Environment */
    protected $environment;

    /** @var string[] */
    protected $files;

    /** @var string[] */
    protected $options;

    /**
     * @param string[] $files
     * @param string[] $options
     */
    public function __construct(Environment $environment, array $files, array $options)
    {
        parent::__construct();

        $this->files = $files;
        $this->environment = $environment;
        $this->options = $options;
    }

    public function getEnvironment() : Environment
    {
        return $this->environment;
    }

    /**
     * @return string[]
     */
    public function getFiles() : array
    {
        return $this->files;
    }

    /**
     * @return string[]
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    public function getDepth() : int
    {
        if (isset($this->options['depth'])) {
            return (int) $this->options['depth'];
        }

        if (isset($this->options['maxdepth'])) {
            return (int) $this->options['maxdepth'];
        }

        return self::DEFAULT_DEPTH;
    }
}
