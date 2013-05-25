<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the BuilderAbstract class.
 */
class BuilderAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var BuilderAbstract $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new BuilderMock();
    }

    /**
     * @covers phpDocumentor\Descriptor\BuilderAbstract::__construct
     * @covers phpDocumentor\Descriptor\BuilderAbstract::getProjectDescriptor
     */
    public function testCreatesAnEmptyProjectDescriptorUponDefaultInitialization()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\ProjectDescriptor', $this->fixture->getProjectDescriptor());
        $this->assertEquals(BuilderAbstract::DEFAULT_PROJECT_NAME, $this->fixture->getProjectDescriptor()->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\BuilderAbstract::__construct
     * @covers phpDocumentor\Descriptor\BuilderAbstract::getProjectDescriptor
     */
    public function testProvidingAPreExistingDescriptorToBuildOn()
    {
        $projectDescriptorName = 'My Descriptor';
        $projectDescriptorMock = new ProjectDescriptor($projectDescriptorName);
        $this->fixture = new BuilderMock($projectDescriptorMock);

        $this->assertSame($projectDescriptorMock, $this->fixture->getProjectDescriptor());
        $this->assertEquals($projectDescriptorName, $this->fixture->getProjectDescriptor()->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\BuilderAbstract::isVisibilityAllowed
     */
    public function testDeterminesWhetherASpecificVisibilityIsAllowedToBeIncluded()
    {
        $projectDescriptorName = 'My Descriptor';
        $projectDescriptorMock = new ProjectDescriptor($projectDescriptorName);
        $projectDescriptorMock->getSettings()->setVisibility(Settings::VISIBILITY_PUBLIC);
        $this->fixture = new BuilderMock($projectDescriptorMock);

        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
    }
}

/**
 * Mock object which enables testing captured elements.
 */
class BuilderMock extends BuilderAbstract
{
    public function buildFile($data)
    {
    }

    public function buildClass($data)
    {
    }

    public function buildInterface($data)
    {
    }

    public function buildTrait($data)
    {
    }

    public function buildFunction($data)
    {
    }

    public function buildConstant($data, $container = null)
    {
    }

    public function buildMethod($data, $container = null)
    {
    }

    public function buildProperty($data, $container = null)
    {
    }
}
