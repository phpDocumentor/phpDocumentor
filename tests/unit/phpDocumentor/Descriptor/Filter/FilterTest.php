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

namespace phpDocumentor\Descriptor\Filter;

use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Filter class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\Filter
 * @covers ::__construct
 */
final class FilterTest extends TestCase
{
    /**
     * @covers ::filter
     */
    public function testFilter() : void
    {
        $filterableMock = $this->prophesize(Filterable::class)->reveal();

        $filterStep = new class implements FilterInterface {
            public function __invoke(Filterable $filterable) : ?Filterable
            {
                return $filterable;
            }
        };

        $filter = new Filter(
            [$filterStep]
        );

        $this->assertSame($filterableMock, $filter->filter($filterableMock));
    }
}
