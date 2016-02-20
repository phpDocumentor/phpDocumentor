<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Renderer;

use Mockery as m;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\RenderActionCompleted
 * @covers ::<private>
 */
final class RenderActionCompletedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::action
     */
    public function itRegistersTheAssociatedAction()
    {
        $action = m::mock(Template\Action::class);

        $event = new RenderActionCompleted($action);

        $this->assertSame($action, $event->action());
    }
}
