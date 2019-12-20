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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Event\PostTransformationEvent
 * @covers ::__construct
 */
final class PostTransformationEventTest extends MockeryTestCase
{
    /** @var PostTransformationEvent $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new PostTransformationEvent(new stdClass());
    }

    /**
     * @covers ::createInstance
     * @covers ::getSubject
     */
    public function testCreatingAnInstance() : void
    {
        $subject = new stdClass();
        $this->fixture = PostTransformationEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }
}
