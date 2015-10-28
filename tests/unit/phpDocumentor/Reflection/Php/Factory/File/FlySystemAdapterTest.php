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

namespace phpDocumentor\Reflection\Php\Factory\File;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

/**
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\File\FlySystemAdapter
 */
class FlySystemAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FlySystemAdapter
     */
    private $fixture;

    private $fileContents = '<?php echo "hello world";';

    protected function setUp()
    {
        $fileSystem = new Filesystem(new MemoryAdapter());
        $fileSystem->write('source.php', $this->fileContents);
        $this->fixture = new FlySystemAdapter($fileSystem);
    }

    /**
     * @covers ::fileExists
     */
    public function testFileExists()
    {
        $this->assertTrue($this->fixture->fileExists('./source.php'));
        $this->assertFalse($this->fixture->fileExists('./other.php'));
    }

    /**
     * @covers ::getContents
     */
    public function testGetContents()
    {
        $this->assertEquals($this->fileContents, $this->fixture->getContents('./source.php'));
    }

    /**
     * @covers ::md5
     */
    public function testMd5()
    {
        $this->assertEquals(md5($this->fileContents), $this->fixture->md5('./source.php'));
    }
}
