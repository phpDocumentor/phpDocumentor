<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Event;

use Mockery as m;

/**
 * Tests the functionality for the PreTransformEvent class.
 */
class PreTransformEventTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var PreTransformEvent $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     * Creates a new DOMDocument object.
     */
    protected function setUp(): void
    {
        $this->fixture = new PreTransformEvent(new \stdClass());
    }

    /**
     * @covers \phpDocumentor\Transformer\Event\PreTransformEvent::getProject
     * @covers \phpDocumentor\Transformer\Event\PreTransformEvent::setProject
     */
    public function testSetAndGetProject() : void
    {
        $project = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $this->assertNull($this->fixture->getProject());

        $this->fixture->setProject($project);

        $this->assertSame($project, $this->fixture->getProject());
    }
}
