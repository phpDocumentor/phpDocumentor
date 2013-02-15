<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Transformer\Transformer.
 *
 * @covers phpDocumentor\Transformer\Transformer
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transformer $fixture */
    protected $fixture = null;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $mock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $mock->shouldIgnoreMissing();
        $this->fixture = new Transformer($mock);
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::__construct
     */
    public function testInitialization()
    {
        $mock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $mock->shouldIgnoreMissing();
        $fixture = new Transformer($mock);

        $this->assertAttributeEquals($mock, 'templates', $fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::getTarget
     * @covers phpDocumentor\Transformer\Transformer::setTarget
     */
    public function testSettingAndGettingATarget()
    {
        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(__DIR__);

        $this->assertEquals(__DIR__, $this->fixture->getTarget());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::setTarget
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenSettingFileAsTarget()
    {
        $this->fixture->setTarget(__FILE__);
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::setTarget
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenUnknownDirectoryIsProvided()
    {
        $this->fixture->setTarget(__DIR__ . '_make_this_unknown');
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::setBehaviours
     * @covers phpDocumentor\Transformer\Transformer::getBehaviours
     */
    public function testProvidingBehaviours()
    {
        $this->assertEquals(null, $this->fixture->getBehaviours());

        $behaviours = m::mock('phpDocumentor\Transformer\Behaviour\Collection');
        $this->fixture->setBehaviours($behaviours);

        $this->assertEquals($behaviours, $this->fixture->getBehaviours());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::getParsePrivate
     * @covers phpDocumentor\Transformer\Transformer::setParsePrivate
     */
    public function testSettingAndGettingPrivateParsing()
    {
        $this->assertEquals(false, $this->fixture->getParsePrivate());

        $this->fixture->setParsePrivate(true);

        $this->assertEquals(true, $this->fixture->getParsePrivate());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::getTemplates
     */
    public function testRetrieveTemplateCollection()
    {
        $mock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $mock->shouldIgnoreMissing();
        $fixture = new Transformer($mock);

        $this->assertEquals($mock, $fixture->getTemplates());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::execute
     */
    public function testExecute()
    {
        $project = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');

        $behaviourCollection = m::mock('phpDocumentor\Transformer\Behaviour\Collection');
        $behaviourCollection ->shouldReceive('process')->with($project);

        $transformation = m::mock('phpDocumentor\Transformer\Transformation')
            ->shouldReceive('execute')->with($project)
            ->shouldReceive('getQuery')->andReturn('')
            ->shouldReceive('getWriter')->andReturn(new \stdClass())
            ->shouldReceive('getArtifact')->andReturn('')
            ->getMock();

        $templateCollection = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollection->shouldReceive('getTransformations')->andReturn(
            array($transformation)
        );

        $fixture = new Transformer($templateCollection);
        $fixture->setBehaviours($behaviourCollection);

        $this->assertNull($fixture->execute($project));
    }

    /**
     * Tests whether the generateFilename method returns a file according to
     * the right format.
     *
     * @covers phpDocumentor\Transformer\Transformer::generateFilename
     *
     * @return void
     */
    public function testGenerateFilename()
    {
        // separate the directories with the DIRECTORY_SEPARATOR constant to prevent failing tests on windows
        $filename = 'directory' . DIRECTORY_SEPARATOR . 'directory2' . DIRECTORY_SEPARATOR . 'file.php';
        $this->assertEquals('directory.directory2.file.html', $this->fixture->generateFilename($filename));
    }
}
