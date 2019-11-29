<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Event;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * Tests the functionality for the PreTransformEvent class.
 */
class PreTransformEventTest extends MockeryTestCase
{
    /** @var PreTransformEvent $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     * Creates a new DOMDocument object.
     */
    protected function setUp() : void
    {
        $this->fixture = new PreTransformEvent(new stdClass());
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
