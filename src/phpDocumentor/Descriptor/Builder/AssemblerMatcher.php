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
    /**
     * @param Matcher<object> $matcher
     * @param AssemblerInterface<Descriptor, object> $assembler
     */
    public function __construct(private readonly Matcher $matcher, private readonly AssemblerInterface $assembler)
    {
    }

    public function match(object $criteria): bool
    {
        $matcher = $this->matcher;

        return $matcher($criteria);
    }

    /** @return AssemblerInterface<Descriptor, object> */
    public function getAssembler(): AssemblerInterface
    {
        return $this->assembler;
    }
}
