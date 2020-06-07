<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreParsingEvent;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\ProjectFactory;
use Psr\Log\NullLogger;
use Symfony\Component\Stopwatch\Stopwatch;
use function ini_set;
use function sys_get_temp_dir;

/**
 * Test class for \phpDocumentor\Parser\Parser.
 *
 * @coversDefaultClass \phpDocumentor\Parser\Parser
 * @covers ::__construct
 * @covers ::<private>
 */
final class ParserTest extends MockeryTestCase
{
    /** @var Parser */
    protected $fixture = null;

    /** @var MockInterface|ProjectFactory */
    private $projectFactory;

    /**
     * Instantiates a new parser object as fixture.
     */
    protected function setUp() : void
    {
        ini_set('zend.script_encoding', '');
        $this->projectFactory = m::mock(ProjectFactory::class);

        $this->fixture = new Parser(
            $this->projectFactory,
            new Stopwatch(),
            new NullLogger()
        );
    }

    /**
     * @covers ::getIgnoredTags
     * @covers ::setIgnoredTags
     */
    public function testSetAndGetIgnoredTags() : void
    {
        $parser = new Parser(
            m::mock(ProjectFactory::class),
            m::mock(Stopwatch::class),
            new NullLogger()
        );
        $this->assertEquals([], $parser->getIgnoredTags());

        $parser->setIgnoredTags(['param']);
        $this->assertEquals(['param'], $parser->getIgnoredTags());
    }

    /**
     * @covers ::setEncoding
     * @covers ::getEncoding
     */
    public function testSettingAndRetrievingTheEncodingOfTheProvidedFiles() : void
    {
        $this->assertEquals('utf-8', $this->fixture->getEncoding());

        $this->fixture->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->fixture->getEncoding());
    }

    /**
     * @covers ::setPath
     * @covers ::getPath
     */
    public function testSettingAndRetrievingTheBasePath() : void
    {
        // Arrange
        $this->assertSame('', $this->fixture->getPath());

        // Act
        $this->fixture->setPath(sys_get_temp_dir());

        // Assert
        $this->assertSame(sys_get_temp_dir(), $this->fixture->getPath());
    }

    /**
     * Tests whether the doValidation() and setValidate methods function
     * properly.
     *
     * @covers ::setValidate
     * @covers ::doValidation
     */
    public function testValidate() : void
    {
        // defaults to false
        $this->assertEquals(false, $this->fixture->doValidation());

        $this->fixture->setValidate(true);
        $this->assertEquals(true, $this->fixture->doValidation());

        $this->fixture->setValidate(false);
        $this->assertEquals(false, $this->fixture->doValidation());
    }

    /**
     * Tests whether the getMarker() and setMarkers methods function
     * properly.
     *
     * @covers ::setMarkers
     * @covers ::getMarkers
     */
    public function testMarkers() : void
    {
        $fixtureData = ['FIXME', 'TODO', 'DOIT'];

        // default is TODO and FIXME
        $this->assertEquals(['TODO', 'FIXME'], $this->fixture->getMarkers());

        $this->fixture->setMarkers($fixtureData);
        $this->assertEquals($fixtureData, $this->fixture->getMarkers());
    }

    /**
     * @covers ::setDefaultPackageName
     * @covers ::getDefaultPackageName
     */
    public function testSetAndGetDefaultPackageName() : void
    {
        $parser = new Parser(
            m::mock(ProjectFactory::class),
            m::mock(Stopwatch::class),
            new NullLogger()
        );

        $this->assertEquals('Default', $parser->getDefaultPackageName());

        $parser->setDefaultPackageName('test');

        $this->assertSame('test', $parser->getDefaultPackageName());
    }

    /**
     * @covers ::parse
     */
    public function testFilesAreParsedByProjectFactory() : void
    {
        $file = new vfsStreamFile('my-file.php');
        vfsStream::setup()->addChild($file);

        $files = [
            new LocalFile($file->url()),
        ];

        $expectedProject = new Project(ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME);
        $this->projectFactory->shouldReceive('create')
            ->with(ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME, $files)
            ->andReturn($expectedProject);

        $project = $this->fixture->parse($files);

        $this->assertSame($expectedProject, $project);
    }

    /**
     * @covers ::parse
     */
    public function testWhenParsingAnnounceWhenYouAreStarting() : void
    {
        $file = new vfsStreamFile('my-file.php');
        vfsStream::setup()->addChild($file);

        $files = [new LocalFile($file->url())];

        $preParsingEvent = null;
        Dispatcher::getInstance()->addListener(
            'parser.pre',
            static function (PreParsingEvent $event) use (&$preParsingEvent) : void {
                $preParsingEvent = $event;
            }
        );

        $this->projectFactory->shouldReceive('create')->andReturn(
            new Project(ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME)
        );

        $this->fixture->parse($files);

        $this->assertInstanceOf(PreParsingEvent::class, $preParsingEvent);
        $this->assertSame(1, $preParsingEvent->getFileCount());
    }
}
