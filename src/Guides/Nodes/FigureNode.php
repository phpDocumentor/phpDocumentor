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

class FigureNode extends Node
{
    /** @var ImageNode */
    protected $image;

    /** @var Node|null */
    protected $document;

    public function __construct(ImageNode $image, ?Node $document = null)
    {
        parent::__construct();

        $this->image = $image;
        $this->document = $document;
    }

    public function getImage(): ImageNode
    {
        return $this->image;
    }

    public function getDocument(): ?Node
    {
        return $this->document;
    }
}
