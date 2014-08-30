<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Descriptor\Collection;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\FileAssembler
 */
class FileAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new FileAssembler();
        $this->fixture->setBuilder($this->getProjectDescriptorBuilderMock());
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @return void
     */
    public function testCreateFileDescriptorFromReflector()
    {
        $filename = 'file.php';
        $content = '<?php ... ?>';
        $hash = md5($content);
        $defaultPackageName = 'Package';

        $abstractDescriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $abstractDescriptor->shouldReceive('getLineNumber')->andReturn(1337);

        $docBlockDescription = new DocBlock\Description(
<<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK
        );

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        $fileReflectorMock = m::mock('phpDocumentor\Reflection\FileReflector');
        $fileReflectorMock->shouldReceive('getName')->andReturn($filename);
        $fileReflectorMock->shouldReceive('getFilename')->andReturn($filename);
        $fileReflectorMock->shouldReceive('getHash')->andReturn($hash);
        $fileReflectorMock->shouldReceive('getContents')->andReturn($content);
        $fileReflectorMock->shouldReceive('getDefaultPackageName')->andReturn($defaultPackageName);
        $fileReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);

        $fileReflectorMock->shouldReceive('getConstants')->once()->andReturn(
            new Collection(array($abstractDescriptor))
        );

        $fileReflectorMock->shouldReceive('getFunctions')->once()->andReturn(
            new Collection(array($abstractDescriptor))
        );

        $fileReflectorMock->shouldReceive('getClasses')->once()->andReturn(
            new Collection(array($abstractDescriptor))
        );

        $fileReflectorMock->shouldReceive('getInterfaces')->once()->andReturn(
            new Collection(array($abstractDescriptor))
        );

        $fileReflectorMock->shouldReceive('getTraits')->once()->andReturn(
            new Collection(array($abstractDescriptor))
        );

        $fileReflectorMock->shouldReceive('getMarkers')->once()->andReturn(
            array('type', 'message', 1337)
        );

        $fileReflectorMock->shouldReceive('getIncludes')->andReturn(new Collection);
        $fileReflectorMock->shouldReceive('getNamespaceAliases')->andReturn(new Collection);

        $descriptor = $this->fixture->create($fileReflectorMock);

        $this->assertSame($filename, $descriptor->getName());
        $this->assertSame($hash, $descriptor->getHash());
        $this->assertSame($content, $descriptor->getSource());
    }

    /**
     * Create a descriptor builder mock
     *
     * @return m\MockInterface
     */
    protected function getProjectDescriptorBuilderMock()
    {
        $projectDescriptorBuilderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');

        $projectDescriptorBuilderMock->shouldReceive('buildDescriptor')->andReturnUsing(function ($param) {
            $mock = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
            $mock->shouldReceive('setLocation')->atLeast()->once();
            $mock->shouldReceive('getTags')->atLeast()->once()->andReturn(new Collection);
            $mock->shouldReceive('getFullyQualifiedStructuralElementName')->once()->andReturn('Frank_is_een_eindbaas');

            return $mock;
        });

        return $projectDescriptorBuilderMock;
    }
}
