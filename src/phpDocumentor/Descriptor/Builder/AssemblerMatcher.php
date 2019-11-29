<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder;

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
     * @param mixed $criteria
     */
    public function match($criteria) : bool
    {
        $matcher = $this->matcher;

        return $matcher($criteria);
    }

    public function getAssembler() : AssemblerInterface
    {
        return $this->assembler;
    }
}
