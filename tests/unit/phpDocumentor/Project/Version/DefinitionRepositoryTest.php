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
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\DomainModel\Version\Definition;
use phpDocumentor\DomainModel\Version\DefinitionFactory;
use phpDocumentor\DomainModel\Version\DefinitionRepository;
use phpDocumentor\DomainModel\VersionNumber;

/**
 * Test case for DefinitionRepository
 *
 * @coversDefaultClass phpDocumentor\Project\Version\DefinitionRepository
 */
class DefinitionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionRepository
     */
    private $fixture;

    /**
     * @var m\Mock
     */
    private $configurationFactoryMock;

    /**
     * @var DefinitionFactory|m\Mock
     */
    private $definitionFactoryMock;

    protected function setUp()
    {
        $this->configurationFactoryMock = m::mock(ConfigurationFactory::class);
        $this->configurationFactoryMock->shouldReceive('get')
            ->andReturn(
                [
                    'phpdocumentor' => [
                        'versions' => [
                            '1.0.0' => [
                                'api' => []
                            ],
                            '1.1.0' => [],
                        ],
                    ],
                ]
            );
        $this->definitionFactoryMock = m::mock(DefinitionFactory::class);
        $this->fixture = new DefinitionRepository($this->configurationFactoryMock, $this->definitionFactoryMock);
    }


    public function testFetchNotExistingVersion()
    {
        $this->assertNull($this->fixture->fetch('notExisting'));
    }

    public function testFetchVersion()
    {
        $this->definitionFactoryMock->shouldReceive('create')
            ->once()
            ->with(['version' => '1.0.0', 'api' => []])
            ->andReturn(new Definition(new VersionNumber(null)));
        $this->assertInstanceOf(Definition::class, $this->fixture->fetch('1.0.0'));
    }

    public function testFetchAll()
    {
        $this->definitionFactoryMock->shouldReceive('create')
            ->times(2)
            ->andReturn(new Definition(new VersionNumber(null)));

        $definitions = $this->fixture->fetchAll();
        $this->assertCount(2, $definitions);
    }
}
