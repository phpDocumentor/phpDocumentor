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

use Symfony\Component\String\Slugger\AsciiSlugger;

class TitleNode extends Node
{
    /** @var SpanNode */
    protected $value;

    /** @var int */
    protected $level;

    /** @var string */
    protected $token;

    /** @var string */
    protected $id;

    /** @var string */
    protected $target = '';

    public function __construct(Node $value, int $level)
    {
        parent::__construct($value);

        $this->level = $level;
        $this->id = (new AsciiSlugger())->slug($this->value->getValue())->lower()->toString();
    }

    public function getValue(): SpanNode
    {
        return $this->value;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
