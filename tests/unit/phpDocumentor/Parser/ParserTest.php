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

namespace phpDocumentor\Parser;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\ProjectFactory;
use Psr\Log\NullLogger;
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

    /**
     * Instantiates a new parser object as fixture.
     */
    protected function setUp() : void
    {
        ini_set('zend.script_encoding', '');
        $this->fixture = new Parser(
            m::mock(ProjectFactory::class),
            m::mock('Symfony\Component\Stopwatch\Stopwatch'),
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
            m::mock('Symfony\Component\Stopwatch\Stopwatch'),
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
        $fixture_data = ['FIXME', 'TODO', 'DOIT'];

        // default is TODO and FIXME
        $this->assertEquals(['TODO', 'FIXME'], $this->fixture->getMarkers());

        $this->fixture->setMarkers($fixture_data);
        $this->assertEquals($fixture_data, $this->fixture->getMarkers());
    }

    /**
     * @covers ::setDefaultPackageName
     * @covers ::getDefaultPackageName
     */
    public function testSetAndGetDefaultPackageName() : void
    {
        $parser = new Parser(
            m::mock(ProjectFactory::class),
            m::mock('Symfony\Component\Stopwatch\Stopwatch'),
            new NullLogger()
        );

        $this->assertEquals('Default', $parser->getDefaultPackageName());

        $parser->setDefaultPackageName('test');

        $this->assertSame('test', $parser->getDefaultPackageName());
    }
}
