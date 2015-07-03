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
 * @coversDefaultClass phpDocumentor\Application\Commands\LoadProjectFromCache
 */
class LoadProjectFromCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getSource
     */
    public function testCanRegisterAbsoluteTargetLocation()
    {
        $fixture = new LoadProjectFromCache(__DIR__);

        $this->assertSame(__DIR__, $fixture->getSource());
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testThrowErrorIfLocationDoesNotExist()
    {
        new LoadProjectFromCache('/khfkjhdskjhfdks');
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testThrowErrorIfLocationIsNotAFolder()
    {
        new LoadProjectFromCache(__FILE__);
    }
}
