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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Event\Mock\EventAbstract as EventAbstractMock;
use stdClass;

/**
 * Test for the EventAbstract class.
 */
class EventAbstractTest extends MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Event\EventAbstract::__construct
     * @covers \phpDocumentor\Event\EventAbstract::getSubject
     */
    public function testSubjectMustBeProvidedAndCanBeRead() : void
    {
        $subject = new stdClass();

        $fixture = new EventAbstractMock($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }

    /**
     * @covers \phpDocumentor\Event\EventAbstract::createInstance
     */
    public function testCanBeConstructedUsingAStaticFactoryMethod() : void
    {
        $subject = new stdClass();

        $fixture = EventAbstractMock::createInstance($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }
}
