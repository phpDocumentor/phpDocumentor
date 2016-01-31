<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Project\Version;

use Mockery as m;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\Definition\Factory;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Version\Definition;
use phpDocumentor\DomainModel\Version\DefinitionFactory;

/**
 * Test case for DefinitionFactory
 *
 * @coversDefaultClass phpDocumentor\Project\Version\DefinitionFactory
 */
class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DefinitionFactory
     */
    private $fixture;

    /**
     * @var m\Mock
     */
    private $apiPHPDocumentGroupDefinitionFactoryMock;

    protected function setUp()
    {
        $this->apiPHPDocumentGroupDefinitionFactoryMock = m::mock(Factory::class);

        $this->fixture = new DefinitionFactory();
        $this->fixture->registerDocumentGroupDefinitionFactory(
            'api',
            new DocumentGroupFormat('php'),
            $this->apiPHPDocumentGroupDefinitionFactoryMock
        );
    }

    /**
     * @covers ::create
     * @covers ::<private>
     */
    public function testCreate()
    {
        $versionConfig = [
            'version' => '1.0.0',
            'api' => [
                'format' => 'php',
            ]
        ];

        $this->apiPHPDocumentGroupDefinitionFactoryMock->shouldReceive('create')->once();

        $versionDefinition = $this->fixture->create($versionConfig);

        $this->assertInstanceOf(Definition::class, $versionDefinition);
        $this->assertCount(1, $versionDefinition->getDocumentGroupDefinitions());
    }

    /**
     * @expectedException phpDocumentor\Exception
     */
    public function testCreateThrowsExceptionWhenTypeDoesnotExist()
    {
        $versionConfig = [
            'version' => '1.0.0',
            'someRandomName' => [
                'format' => 'php',
            ]
        ];

        $this->fixture->create($versionConfig);
    }
}
