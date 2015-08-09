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

use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DocumentGroupDefinition as DocumentGroupDefinitionInterface;
use phpDocumentor\DocumentGroupFormat;

/**
 * @coversDefaultClass phpDocumentor\ApiReference\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new Factory();
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(m::mock(DocumentGroupDefinitionInterface::class)));
        $this->assertTrue($this->fixture->matches(new DocumentGroupDefinition(new DocumentGroupFormat('php'), m::mock(FilesystemInterface::class))));
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $format = new DocumentGroupFormat('php');
        $definition = new DocumentGroupDefinition($format, m::mock(FilesystemInterface::class));

        $api = $this->fixture->create($definition);

        $this->assertSame($format, $api->getFormat());
    }
}
