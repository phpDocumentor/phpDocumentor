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

class DefinitionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionRepository
     */
    private $fixture;

    private $configurationFactoryMock;

    /**
     * @var DefinitionFactory|m\Mock
     */
    private $definitionFactoryMock;

    protected function setUp()
    {
        $this->configurationFactoryMock = m::mock(\stdClass::class);
        $this->configurationFactoryMock->shouldReceive('get')
            ->andReturn(
                [
                    'version' => [
                        '1.0.0' => [],
                        '1.1.0' => [],
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
            ->andReturn(new Definition());
        $this->assertInstanceOf(Definition::class, $this->fixture->fetch('1.0.0'));
    }

    public function testFetchAll()
    {
        $this->definitionFactoryMock->shouldReceive('create')
            ->times(2)
            ->andReturn(new Definition());

        $definitions = $this->fixture->fetchAll();
        $this->assertCount(2, $definitions);
    }
}