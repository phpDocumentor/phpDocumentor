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

/**
 * @template TDescriptor as \phpDocumentor\Descriptor\Descriptor
 * @template TInput as object
 */
final class AssemblerMatcher
{
    /** @var callable */
    private $matcher;

    /** @var AssemblerInterface<TDescriptor, TInput> */
    private $assembler;

    /**
     * @param AssemblerInterface<TDescriptor, TInput> $assembler
     */
    public function __construct(callable $matcher, AssemblerInterface $assembler)
    {
        $this->matcher   = $matcher;
        $this->assembler = $assembler;
    }

    /**
     * @param TInput $criteria
     */
    public function match(object $criteria) : bool
    {
        $matcher = $this->matcher;

        return $matcher($criteria);
    }

    /**
     * @return AssemblerInterface<TDescriptor, TInput>
     */
    public function getAssembler() : AssemblerInterface
    {
        return $this->assembler;
    }
}
