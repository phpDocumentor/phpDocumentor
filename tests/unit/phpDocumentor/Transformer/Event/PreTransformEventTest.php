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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Event\PreTransformEvent
 * @covers ::__construct
 */
final class PreTransformEventTest extends TestCase
{
    use ProphecyTrait;

    /** @var PreTransformEvent $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new PreTransformEvent(new stdClass());
    }

    /**
     * @covers ::createInstance
     * @covers ::getSubject
     */
    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PreTransformEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    /**
     * @covers ::getDocumentationSetDescriptor
     * @covers ::setDocumentationSet
     */
    public function testSetAndGetProject(): void
    {
        $project = $this->prophesize(ProjectDescriptor::class);
        $this->assertNull($this->fixture->getDocumentationSetDescriptor());

        $this->fixture->setDocumentationSet($project->reveal());

        $this->assertSame($project->reveal(), $this->fixture->getDocumentationSetDescriptor());
    }
}
