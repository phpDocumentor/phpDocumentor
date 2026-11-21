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

namespace phpDocumentor\Event;

use phpDocumentor\Event\Mock\EventAbstract as EventAbstractMock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Test for the EventAbstract class.
 */
#[CoversClass(EventAbstract::class)]
class EventAbstractTest extends TestCase
{
    public function testSubjectMustBeProvidedAndCanBeRead(): void
    {
        $subject = new stdClass();

        $fixture = new EventAbstractMock($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }

    public function testCanBeConstructedUsingAStaticFactoryMethod(): void
    {
        $subject = new stdClass();

        $fixture = EventAbstractMock::createInstance($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }
}
