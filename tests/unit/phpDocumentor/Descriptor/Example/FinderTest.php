<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Example;

use org\bovigo\vfs\vfsStream;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Tests for the \phpDocumentor\Descriptor\Example\Finder class.
 */
class FinderTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_TEXT = 'This is an example';

    /** @var Filesystem */
    private $filesystem;

    /** @var Finder */
    private $fixture;

    /**
     * Initializes the fixture.
     */
    protected function setUp()
    {
        $this->filesystem = new Filesystem();
        $this->fixture = new Finder();
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::setSourceDirectory
     * @covers phpDocumentor\Descriptor\Example\Finder::getSourceDirectory
     */
    public function testGetAndSetSourceDirectory()
    {
        $this->assertSame('', $this->fixture->getSourceDirectory());

        $this->fixture->setSourceDirectory('this/is/a/test');

        $this->assertSame('this/is/a/test', $this->fixture->getSourceDirectory());
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::setExampleDirectories
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleDirectories
     */
    public function testGetAndSetExampleDirectories()
    {
        $this->assertSame(array(), $this->fixture->getExampleDirectories());

        $this->fixture->setExampleDirectories(array('this/is/a/test'));

        $this->assertSame(array('this/is/a/test'), $this->fixture->getExampleDirectories());
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::find
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleFileContents
     * @covers phpDocumentor\Descriptor\Example\Finder::constructExamplePath
     */
    public function testFindExampleContentsInExampleDirectory()
    {
        $directories = array(vfsStream::url('base/exampleDirectory'), vfsStream::url('base/exampleDirectory2'));

        $descriptor = $this->givenADescriptorWithExamplePath('example.txt');
        $this->givenTheDirectoryStructure(
            array(
                'exampleDirectory' => array(),
                'exampleDirectory2' => array('example.txt' => self::EXAMPLE_TEXT),
                'source' => array('example.txt' => 'this is not it') // check if the example directory precedes this
            )
        );

        $this->fixture->setExampleDirectories($directories);
        $this->fixture->setSourceDirectory(vfsStream::url('base/source'));
        $result = $this->fixture->find($descriptor);

        $this->assertSame(self::EXAMPLE_TEXT, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::find
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleFileContents
     * @covers phpDocumentor\Descriptor\Example\Finder::getExamplePathFromSource
     */
    public function testFindExampleContentsInSourceDirectory()
    {
        $descriptor = $this->givenADescriptorWithExamplePath('example.txt');
        $this->givenTheDirectoryStructure(array('source' => array('example.txt' => self::EXAMPLE_TEXT)));

        $this->fixture->setSourceDirectory(vfsStream::url('base/source'));
        $result = $this->fixture->find($descriptor);

        $this->assertSame(self::EXAMPLE_TEXT, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::find
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleFileContents
     * @covers phpDocumentor\Descriptor\Example\Finder::getExamplePathFromExampleDirectory
     */
    public function testFindExampleContentsInExamplesDirectoryOfWorkingDirectory()
    {
        // can't use vfsStream because we are working from the Current Working Directory, which is not
        // supported by vfsStream
        $workingDirectory = sys_get_temp_dir() . '/phpdoc-tests';
        $this->givenExampleFileInFolder($workingDirectory .'/examples/example.txt');

        $descriptor = $this->givenADescriptorWithExamplePath('example.txt');

        chdir($workingDirectory);
        $result = $this->fixture->find($descriptor);

        $this->assertSame(self::EXAMPLE_TEXT, $result);

        $this->filesystem->remove($workingDirectory);
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::find
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleFileContents
     */
    public function testFindExampleContentsInCurrentWorkingDirectory()
    {
        // can't use vfsStream because we are working from the Current Working Directory, which is not
        // supported by vfsStream
        $workingDirectory = sys_get_temp_dir() . '/phpdoc-tests';
        $this->givenExampleFileInFolder($workingDirectory .'/example.txt');

        $descriptor = $this->givenADescriptorWithExamplePath('example.txt');

        chdir($workingDirectory);
        $result = $this->fixture->find($descriptor);

        $this->assertSame(self::EXAMPLE_TEXT, $result);

        $this->filesystem->remove($workingDirectory);
    }

    /**
     * @covers phpDocumentor\Descriptor\Example\Finder::find
     * @covers phpDocumentor\Descriptor\Example\Finder::getExampleFileContents
     */
    public function testErrorMessageIsReturnedIfFileIsNotFound()
    {
        $filename = 'doesNotExist.txt';
        $descriptor = $this->givenADescriptorWithExamplePath($filename);

        $result = $this->fixture->find($descriptor);

        $this->assertSame("** File not found : {$filename} **", $result);
    }

    /**
     * Returns an ExampleDescriptor with the given filename set.
     *
     * @param string $path
     *
     * @return ExampleDescriptor
     */
    private function givenADescriptorWithExamplePath($path)
    {
        $descriptor = new ExampleDescriptor('example');
        $descriptor->setFilePath($path);

        return $descriptor;
    }

    /**
     * Initializes a virtual folder structure used to verify file io operations.
     *
     * @param string[] $structure
     *
     * @return void
     */
    private function givenTheDirectoryStructure(array $structure)
    {
        vfsStream::setup('base', null, $structure);
    }

    /**
     * Creates an example file at the given path and creates folders where necessary.
     *
     * @param string $exampleFilename
     *
     * @return void
     */
    private function givenExampleFileInFolder($exampleFilename)
    {
        $this->filesystem->dumpFile($exampleFilename, self::EXAMPLE_TEXT);
    }
}
