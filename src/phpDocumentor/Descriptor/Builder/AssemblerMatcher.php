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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Descriptor;

final class AssemblerMatcher
{
    /** @var Matcher<object> */
    private $matcher;

    /** @var AssemblerInterface<Descriptor, object> */
    private $assembler;

    /**
     * @param Matcher<object> $matcher
     * @param AssemblerInterface<Descriptor, object> $assembler
     */
    public function __construct(Matcher $matcher, AssemblerInterface $assembler)
    {
        $this->matcher   = $matcher;
        $this->assembler = $assembler;
    }

    public function match(object $criteria): bool
    {
        $matcher = $this->matcher;

        return $matcher($criteria);
    }

    /**
     * @return AssemblerInterface<Descriptor, object>
     */
    public function getAssembler(): AssemblerInterface
    {
        return $this->assembler;
    }
}
