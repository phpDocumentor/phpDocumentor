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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Transformer\Writer\Graph\Generator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Graph */
final class GraphTest extends TestCase
{
    use ProphecyTrait;

    private Graph $graph;

    protected function setUp(): void
    {
        $this->graph = new Graph($this->prophesize(Generator::class)->reveal());
    }

    public function testItExposesCustomSettingToEnableGraphs(): void
    {
        $this->assertSame(['graphs.enabled' => false], $this->graph->getDefaultSettings());
    }
}
