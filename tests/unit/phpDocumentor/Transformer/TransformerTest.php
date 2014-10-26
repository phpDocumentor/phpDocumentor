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
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Test class for \phpDocumentor\Transformer\Transformer.
 *
 * @covers phpDocumentor\Transformer\Transformer
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Max length of description printed.
     *
     * @var int
     */
    protected static $MAX_DESCRIPTION_LENGTH = 68;

    /** @var Transformer $fixture */
    protected $fixture = null;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();

        $this->fixture = new Transformer($templateCollectionMock, $writerCollectionMock);
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::__construct
     */
    public function testInitialization()
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();
        $this->fixture = new Transformer($templateCollectionMock, $writerCollectionMock);

        $this->assertAttributeEquals($templateCollectionMock, 'templates', $this->fixture);
        $this->assertAttributeEquals($writerCollectionMock, 'writers', $this->fixture);
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
     * @expectedExceptionMessage Target directory (vfs://myroot) does not exist and could not be created
     */
    public function testExceptionWhenSettingExistingDirAsTarget()
    {
        $fileSystem = \org\bovigo\vfs\vfsStream::setup('myroot');
        $this->fixture->setTarget(\org\bovigo\vfs\vfsStream::url('myroot'));
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::getTemplates
     */
    public function testRetrieveTemplateCollection()
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();

        $fixture = new Transformer($templateCollectionMock, $writerCollectionMock);

        $this->assertEquals($templateCollectionMock, $fixture->getTemplates());
    }

    /**
     * @covers phpDocumentor\Transformer\Transformer::execute
     */
    public function testExecute()
    {
        $myTestWritter = 'myTestWriter';

        $templateCollection = m::mock('phpDocumentor\Transformer\Template\Collection');

        $project = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');

        $myTestWritterMock = m::mock('phpDocumentor\Transformer\Writer\WriterAbstract')
            ->shouldReceive('transform')->getMock();

        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection')
                ->shouldReceive('offsetGet')->with($myTestWritter)->andReturn($myTestWritterMock)
                ->getMock();

        $fixture = new Transformer($templateCollection, $writerCollectionMock);

        $transformation = m::mock('phpDocumentor\Transformer\Transformation')
            ->shouldReceive('execute')->with($project)
            ->shouldReceive('getQuery')->andReturn('')
            ->shouldReceive('getWriter')->andReturn($myTestWritter)
            ->shouldReceive('getArtifact')->andReturn('')
            ->shouldReceive('setTransformer')->with($fixture)
            ->getMock();

        $templateCollection->shouldReceive('getTransformations')->andReturn(
            array($transformation)
        );

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

    /**
     * @covers phpDocumentor\Transformer\Transformer::getDescription
     */
    public function testGetDescription()
    {
        $description = $this->fixture->getDescription();
        $this->assertNotNull($description);
        $this->assertLessThanOrEqual(static::$MAX_DESCRIPTION_LENGTH, strlen($description));
    }
}
