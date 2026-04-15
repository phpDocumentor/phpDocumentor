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

namespace phpDocumentor\Parser\Event;

use phpDocumentor\Event\EventAbstract;
use PHPUnit\Framework\TestCase;
use stdClass;

/** @coversDefaultClass \phpDocumentor\Parser\Event\PreParsingEvent */
final class PreParsingEventTest extends TestCase
{
    private EventAbstract|null $fixture = null;

    public function testCreatingAnInstance(): void
    {
        $subject = new stdClass();
        $this->fixture = PreParsingEvent::createInstance($subject);
        $this->assertSame($subject, $this->fixture->getSubject());
    }

    public function testSettingAndGettingTheFileCount(): void
    {
        $event = new PreParsingEvent(new stdClass());
        $event->setFileCount(42);

        $this->assertSame(42, $event->getFileCount());
    }
}
