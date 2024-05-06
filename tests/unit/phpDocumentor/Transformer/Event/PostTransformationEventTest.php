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

use phpDocumentor\Event\EventAbstract;
use PHPUnit\Framework\TestCase;
use stdClass;

/** @coversDefaultClass \phpDocumentor\Transformer\Event\PostTransformationEvent */
final class PostTransformationEventTest extends TestCase
{
    private PostTransformationEvent|EventAbstract $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new PostTransformationEvent(new stdClass());
    }

    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PostTransformationEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }
}
