<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\DumpAstToDisk
 */
class DumpAstToDiskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getLocation
     */
    public function testCanRegisterAbsoluteTargetLocation()
    {
        $fixture = new DumpAstToDisk(__DIR__);

        $this->assertSame(__DIR__, $fixture->getLocation());
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testErrorIsThrownIfLocationIsEmpty()
    {
        new DumpAstToDisk('');
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testErrorIsThrownIfLocationIsNotAString()
    {
        new DumpAstToDisk([]);
    }
}
