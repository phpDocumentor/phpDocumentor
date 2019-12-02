<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File;
use function md5;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\FileAssembler
 */
class FileAssemblerTest extends MockeryTestCase
{
    /** @var FileAssembler $fixture */
    protected $fixture;

    /** @var PackageDescriptor */
    private $defaultPackage;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
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
        $this->markTestIncomplete('Review this whole test; it feels like it needs to be redone');

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
        //TODO: check this when we are testing default package behavior
        //$this->assertSame($this->defaultPackage, $descriptor->getPackage());
    }

    /**
     * Create a descriptor builder mock
     */
    protected function getProjectDescriptorBuilderMock() : MockInterface
    {
        $projectDescriptorBuilderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $projectDescriptorBuilderMock->shouldReceive('getDefaultPackage')
            ->andReturn($this->defaultPackage);

        $projectDescriptorBuilderMock->shouldReceive(
            'getProjectDescriptor->getSettings->shouldIncludeSource'
        )->andReturn(true);
        $projectDescriptorBuilderMock->shouldReceive('buildDescriptor')->andReturnUsing(
            static function ($param) {
                $mock = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
                $mock->shouldReceive('setLocation')->atLeast()->once();
                $mock->shouldReceive('getTags')->atLeast()->once()->andReturn(new Collection());
                $mock->shouldReceive('getFullyQualifiedStructuralElementName')
                    ->once()
                    ->andReturn('Frank_is_een_eindbaas');

                return $mock;
            }
        );

        return $projectDescriptorBuilderMock;
    }
}
