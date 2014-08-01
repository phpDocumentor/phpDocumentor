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

namespace phpDocumentor\Parser;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Event\Dispatcher;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /** @var File */
    private $fixture;

    /** @var Parser|m\MockInterface */
    private $parserMock;

    /** @var Dispatcher|m\MockInterface */
    private $dispatcherMock;

    /**
     * Initializes a default fixture with the parser dependency.
     */
    protected function setUp()
    {
        // override dispatcher, we do not want to test this by default
        $this->dispatcherMock = m::mock('phpDocumentor\Event\Dispatcher')->shouldIgnoreMissing();
        Dispatcher::setInstance('default', $this->dispatcherMock);

        $this->parserMock = m::mock('phpDocumentor\Parser\Parser');
        $this->fixture = new File($this->parserMock);
    }

    /**
     * @covers phpDocumentor\Parser\File::__construct
     */
    public function testParserIsRegistered()
    {
        $this->assertAttributeSame($this->parserMock, 'parser', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Parser\File::parse
     * @covers phpDocumentor\Parser\File::createFileReflector
     * @covers phpDocumentor\Parser\File::getRelativeFilename
     */
    public function testCreateFileDescriptorInBuilderForNewFile()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(false);

        $fileDescriptor = $this->givenAFileDescriptorWithHash('12345');
        $builder        = $this->givenABuilderMock();

        $this->whenBuilderDoesNotHaveFileDescriptorInitially($builder, $fileDescriptor);
        $this->thenBuilderShouldBuildAFileDescriptor($builder);

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Parser\File::parse
     */
    public function testDoNotCreateFileDescriptorInBuilderForExistingFile()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(false);

        $fileDescriptor = $this->givenAFileDescriptorWithHash(md5(file_get_contents(__FILE__)));
        $builder        = $this->givenABuilderMock();

        $this->whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor);
        $this->thenBuilderShouldNotBuildFileDescriptor($builder);

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Parser\File::parse
     * @covers phpDocumentor\Parser\File::createFileReflector
     */
    public function testCreateFileDescriptorInBuilderForExistingFileWhenParserIsForced()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(true);

        $fileDescriptor = $this->givenAFileDescriptorWithHash(md5(file_get_contents(__FILE__)));
        $builder        = $this->givenABuilderMock();

        $this->whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor);
        $this->thenBuilderShouldBuildAFileDescriptor($builder);

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Parser\File::parse
     * @covers phpDocumentor\Parser\File::createFileReflector
     */
    public function testCreateFileDescriptorInBuilderForExistingButChangedFile()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(false);

        $fileDescriptor = $this->givenAFileDescriptorWithHash('12345');
        $builder        = $this->givenABuilderMock();

        $this->whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor);
        $this->thenBuilderShouldBuildAFileDescriptor($builder);

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Parser\File::parse
     */
    public function testIfParsingErrorsAreLogged()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(false);

        $fileDescriptor = $this->givenAFileDescriptorWithHash('12345');
        $builder        = $this->givenABuilderMock();

        $this->whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor);
        $this->thenBuilderShouldFailWhileBuildingAFileDescriptor($builder);
        $this->thenDispatcherShouldReceiveLoggingEvents();

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Parser\File::logErrorsForDescriptor
     * @covers phpDocumentor\Parser\File::log
     */
    public function testIfAllErrorsAreLogged()
    {
        // Arrange
        $this->initializeParserWithDefaultVariables(false);

        $fileDescriptor = $this->givenAFileDescriptorWithHash('12345');
        $builder        = $this->givenABuilderMock();

        $this->whenFileDescriptorContainsErrors($fileDescriptor);
        $this->whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor);
        $this->thenDispatcherShouldReceiveLoggingEvents();

        // Act
        $this->fixture->parse(__FILE__, $builder);

        $this->assertTrue(true);
    }

    /**
     * @param $forced
     */
    protected function initializeParserWithDefaultVariables($forced)
    {
        $this->parserMock->shouldReceive('getDefaultPackageName')->andReturn('default');
        $this->parserMock->shouldReceive('doValidation')->andReturn(false);
        $this->parserMock->shouldReceive('getEncoding')->andReturn('utf-8');
        $this->parserMock->shouldReceive('getMarkers')->andReturn(array('TODO', 'FIXME'));
        $this->parserMock->shouldReceive('isForced')->andReturn($forced);
        $this->parserMock->shouldReceive('getPath')->andReturn(__DIR__);
    }

    /**
     * @return m\MockInterface
     */
    protected function givenABuilderMock()
    {
        return m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder')->shouldIgnoreMissing();
    }

    /**
     * @param string $hash
     *
     * @return FileDescriptor
     */
    protected function givenAFileDescriptorWithHash($hash)
    {
        return new FileDescriptor($hash);
    }

    /**
     * @param m\MockInterface $builder
     * @param FileDescriptor  $fileDescriptor
     */
    protected function whenBuilderAlwaysReturnsTheFileDescriptor($builder, $fileDescriptor)
    {
        $builder->shouldReceive('getProjectDescriptor->getFiles->get')->atLeast(1)
            ->with(basename(__FILE__))
            ->andReturn($fileDescriptor);
    }

    /**
     * @param m\MockInterface $builder
     */
    protected function thenBuilderShouldBuildAFileDescriptor($builder)
    {
        $builder->shouldReceive('buildFileUsingSourceData')->atLeast(1)
            ->with(m::type('phpDocumentor\Reflection\FileReflector'));
    }

    /**
     * @param m\MockInterface $builder
     */
    protected function thenBuilderShouldFailWhileBuildingAFileDescriptor($builder)
    {
        $builder->shouldReceive('buildFileUsingSourceData')->atLeast(1)
            ->with(m::type('phpDocumentor\Reflection\FileReflector'))
            ->andThrow('phpDocumentor\Parser\Exception');
    }

    /**
     * @param m\MockInterface $builder
     */
    protected function thenBuilderShouldNotBuildFileDescriptor($builder)
    {
        $builder->shouldReceive('buildFileUsingSourceData')->never();
    }

    /**
     * @param m\MockInterface $builder
     * @param FileDescriptor  $fileDescriptor
     */
    protected function whenBuilderDoesNotHaveFileDescriptorInitially($builder, $fileDescriptor)
    {
        $builder->shouldReceive('getProjectDescriptor->getFiles->get')->atLeast(1)
            ->with(basename(__FILE__))->andReturn(null, $fileDescriptor);
    }

    protected function thenDispatcherShouldReceiveLoggingEvents()
    {
        $this->dispatcherMock->shouldReceive('dispatch')->atLeast(1)
            ->with('system.log', m::type('phpDocumentor\Event\LogEvent'));
    }

    /**
     * @param $fileDescriptor
     */
    protected function whenFileDescriptorContainsErrors($fileDescriptor)
    {
        $fileDescriptor->getErrors()->add(new Error('ERROR', '12345', 10));
    }
}
