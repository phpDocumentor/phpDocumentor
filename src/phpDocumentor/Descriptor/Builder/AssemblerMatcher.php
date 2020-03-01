<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\File;

final class AssemblerMatcher
{
    /** @var callable */
    private $matcher;

    /** @var AssemblerInterface */
    private $assembler;

    public function __construct(callable $matcher, AssemblerInterface $assembler)
    {
        $this->matcher   = $matcher;
        $this->assembler = $assembler;
    }

    /**
     * @param Element|File|Tag|Argument $criteria
     */
    public function match(object $criteria) : bool
    {
        $matcher = $this->matcher;

        return $matcher($criteria);
    }

    public function getAssembler() : AssemblerInterface
    {
        return $this->assembler;
    }
}
