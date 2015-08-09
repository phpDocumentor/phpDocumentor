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

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Cache;
use Mockery as m;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\DumpAstToDiskHandler
 */
class DumpAstToDiskHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analyzer|m\MockInterface */
    private $analyzer;

    /** @var DumpAstToDiskHandler */
    private $fixture;

    public function setUp()
    {
        $this->analyzer = m::mock(Analyzer::class);
        $this->fixture = new DumpAstToDiskHandler($this->analyzer);
    }

    /**
     * @covers ::__construct
     * @covers ::__invoke
     * @uses phpDocumentor\Descriptor\ProjectDescriptor
     */
    public function testProjectGetsSerializedAndDumpedToDisk()
    {
        $this->markTestIncomplete('Shall be rewritten after new reflection integration.');
        $target = sys_get_temp_dir() . '/phpdoc.ast';
        $projectDescriptor = new ProjectDescriptor('');

        $this->analyzer->shouldReceive('getProjectDescriptor')->andReturn($projectDescriptor);

        $this->fixture->__invoke(new DumpAstToDisk($target));

        $this->assertFileExists($target);
        $this->assertEquals($projectDescriptor, unserialize(file_get_contents($target)));
        unlink($target);
    }
}
