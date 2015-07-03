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
 * @coversDefaultClass phpDocumentor\Application\Commands\CacheProject
 */
class CacheProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTarget
     */
    public function testCanRegisterAbsoluteTargetLocation()
    {
        $fixture = new CacheProject(__DIR__);

        $this->assertSame(__DIR__, $fixture->getTarget());
    }

    /**
     * @covers ::__construct
     * @covers ::getTarget
     */
    public function testCanRegisterTargetLocationRelativeToCurrentWorkingDirectory()
    {
        $cwd = getcwd();
        chdir(__DIR__ . '/..');
        $fixture = new CacheProject('Commands');
        chdir($cwd);

        $this->assertSame(__DIR__, $fixture->getTarget());
    }

    /**
     * @covers ::__construct
     * @covers ::getTarget
     */
    public function testCreatesDirectoryIfMissing()
    {
        $directory = sys_get_temp_dir() . '/this-directory-is-not-here';
        $this->assertFalse(file_exists($directory));

        $fixture = new CacheProject($directory);

        $this->assertTrue(file_exists($directory));
        rmdir(sys_get_temp_dir() . '/this-directory-is-not-here');
        $this->assertSame($directory, $fixture->getTarget());
    }

    /**
     * @covers ::__construct
     * @covers ::getTarget
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfAFolderCannotBeCreated()
    {
        $directory = sys_get_temp_dir() . "/in\0alid";

        new CacheProject($directory);
    }
}
