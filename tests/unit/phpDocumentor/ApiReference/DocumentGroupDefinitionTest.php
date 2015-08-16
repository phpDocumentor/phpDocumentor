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

namespace phpDocumentor\ApiReference;

use Flyfinder\Specification\SpecificationInterface;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DocumentGroupFormat;

class DocumentGroupDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFiles()
    {
        $fileSystemMock = m::mock(FileSystemInterface::class);
        $specificationMock = m::mock(SpecificationInterface::class);
        $definition = new DocumentGroupDefinition(new DocumentGroupFormat('PHP'), $fileSystemMock, $specificationMock);

        $result = ['myFile.php', ['someFile.php']];

        $fileSystemMock->shouldReceive('find')->with($specificationMock)->andReturn(new \ArrayIterator($result));

        $this->assertEquals($result, $definition->getFiles());
    }
}
