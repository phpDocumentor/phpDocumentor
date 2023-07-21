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

use phpDocumentor\Configuration\ApiSpecification;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the Filter class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\Filter
 * @covers ::__construct
 */
final class FilterTest extends TestCase
{
    use ProphecyTrait;

    /** @covers ::filter */
    public function testFilter(): void
    {
        $filterableMock = $this->prophesize(Filterable::class)->reveal();

        $filterStep = new class implements FilterInterface {
            public function __invoke(FilterPayload $payload): FilterPayload
            {
                return $payload;
            }
        };

        $filter = new Filter(
            [$filterStep],
        );

        $this->assertSame($filterableMock, $filter->filter($filterableMock, ApiSpecification::createDefault()));
    }
}
