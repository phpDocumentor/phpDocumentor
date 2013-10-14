<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Event;

use Mockery as m;
use phpDocumentor\Event\Mock\EventAbstract as EventAbstractMock;

/**
 * Test for the EventAbstract class.
 */
class EventAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Event\EventAbstract::__construct
     * @covers phpDocumentor\Event\EventAbstract::getSubject
     */
    public function testSubjectMustBeProvidedAndCanBeRead()
    {
        $subject = new \stdClass();

        $fixture = new EventAbstractMock($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }

    /**
     * @covers phpDocumentor\Event\EventAbstract::createInstance
     */
    public function testCanBeConstructedUsingAStaticFactoryMethod()
    {
        $subject = new \stdClass();

        $fixture = EventAbstractMock::createInstance($subject);

        $this->assertSame($subject, $fixture->getSubject());
    }
}
