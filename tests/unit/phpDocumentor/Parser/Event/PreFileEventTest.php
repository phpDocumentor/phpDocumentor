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

namespace phpDocumentor\Parser\Event;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

/**
 * Test for the PreFileEvent class.
 */
class PreFileEventTest extends MockeryTestCase
{
    /** @var PreFileEvent $fixture */
    protected $fixture;

    /**
     * Sets up a fixture.
     */
    protected function setUp() : void
    {
        $this->fixture = new PreFileEvent(new stdClass());
    }

    /**
     * @covers \phpDocumentor\Parser\Event\PreFileEvent::getFile
     * @covers \phpDocumentor\Parser\Event\PreFileEvent::setFile
     */
    public function testRemembersFileThatTriggersIt() : void
    {
        $filename = 'myfile.txt';

        $this->assertEmpty($this->fixture->getFile());

        $this->fixture->setFile($filename);

        $this->assertSame($filename, $this->fixture->getFile());
    }
}
