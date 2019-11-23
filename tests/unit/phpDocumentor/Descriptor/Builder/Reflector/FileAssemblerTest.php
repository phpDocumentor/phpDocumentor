<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\PackageDescriptor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\FileAssembler
 */
class FileAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var FileAssembler $fixture */
    protected $fixture;

    /** @var PackageDescriptor */
    private $defaultPackage;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->fixture = new FileAssembler();
        $this->fixture->setBuilder($this->getProjectDescriptorBuilderMock());
        $this->defaultPackage = new PackageDescriptor();
        $this->defaultPackage->setName('\\PhpDocumentor');
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateFileDescriptorFromReflector() : void
    {
        $filename = 'file.php';
        $content = '<?php ... ?>';
        $hash = md5($content);

        $abstractDescriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $abstractDescriptor->shouldReceive('getLineNumber')->andReturn(1337);

        $docBlockDescription = new DocBlock\Description(
            <<<DOCBLOCK
            /**
             * This is a example description
             */
DOCBLOCK
        );

        $docBlockMock = new DocBlock('This is a example description', $docBlockDescription);

        $fileReflectorMock = new File($hash, $filename, $content, $docBlockMock);
//        $fileReflectorMock->shouldReceive('getConstants')->once()->andReturn(
//            new Collection(array($abstractDescriptor))
//        );
//
//        $fileReflectorMock->shouldReceive('getFunctions')->once()->andReturn(
//            new Collection(array($abstractDescriptor))
//        );
//
//        $fileReflectorMock->shouldReceive('getClasses')->once()->andReturn(
//            new Collection(array($abstractDescriptor))
//        );
//
//        $fileReflectorMock->shouldReceive('getInterfaces')->once()->andReturn(
//            new Collection(array($abstractDescriptor))
//        );
//
//        $fileReflectorMock->shouldReceive('getTraits')->once()->andReturn(
//            new Collection(array($abstractDescriptor))
//        );
//
//        $fileReflectorMock->shouldReceive('getMarkers')->once()->andReturn(
//            array('type', 'message', 1337)
//        );
//
//        $fileReflectorMock->shouldReceive('getIncludes')->andReturn(new Collection);
//        $fileReflectorMock->shouldReceive('getNamespaceAliases')->andReturn(new Collection);

        $descriptor = $this->fixture->create($fileReflectorMock);

        $this->assertSame($filename, $descriptor->getName());
        $this->assertSame($hash, $descriptor->getHash());
        $this->assertSame($content, $descriptor->getSource());
        //TODO: check this when we are testing default package behavoir
        //$this->assertSame($this->defaultPackage, $descriptor->getPackage());
    }

    /**
     * Create a descriptor builder mock
     *
     * @return m\MockInterface
     */
    protected function getProjectDescriptorBuilderMock() : \Mockery\MockInterface
    {
        $projectDescriptorBuilderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $projectDescriptorBuilderMock->shouldReceive('getDefaultPackage')
            ->andReturn($this->defaultPackage);

        $projectDescriptorBuilderMock->shouldReceive(
            'getProjectDescriptor->getSettings->shouldIncludeSource'
        )->andReturn(true);
        $projectDescriptorBuilderMock->shouldReceive('buildDescriptor')->andReturnUsing(function ($param) {
            $mock = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
            $mock->shouldReceive('setLocation')->atLeast()->once();
            $mock->shouldReceive('getTags')->atLeast()->once()->andReturn(new Collection());
            $mock->shouldReceive('getFullyQualifiedStructuralElementName')
                ->once()
                ->andReturn('Frank_is_een_eindbaas');

            return $mock;
        });

        return $projectDescriptorBuilderMock;
    }
}
