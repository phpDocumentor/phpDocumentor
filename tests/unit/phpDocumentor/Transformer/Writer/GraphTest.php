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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Transformer\Writer\Graph\GraphVizClassDiagram;
use phpDocumentor\Transformer\Writer\Graph\PlantumlClassDiagram;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Graph
 * @covers ::__construct
 * @covers ::<private>
 */
final class GraphTest extends MockeryTestCase
{
    private Graph $graph;

    protected function setUp(): void
    {
        $this->graph = new Graph(
            new GraphVizClassDiagram(),
            new PlantumlClassDiagram(new NullLogger(), m::mock(PlantumlRenderer::class)),
        );
    }

    /**
     * @covers ::getDefaultSettings
     */
    public function testItExposesCustomSettingToEnableGraphs(): void
    {
        $this->assertSame(['graphs.enabled' => false], $this->graph->getDefaultSettings());
    }
}
