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

namespace phpDocumentor\Transformer;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;
use Psr\Log\NullLogger;
use const DIRECTORY_SEPARATOR;
use function strlen;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Transformer
 */
final class TransformerTest extends MockeryTestCase
{
    /** @var int Max length of description printed. */
    private static $MAX_DESCRIPTION_LENGTH = 68;

    /** @var Transformer $fixture */
    private $fixture = null;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     */
    protected function setUp() : void
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();

        $this->fixture = new Transformer($templateCollectionMock, $writerCollectionMock, new NullLogger());
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization() : void
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();
        $this->fixture = new Transformer($templateCollectionMock, $writerCollectionMock, new NullLogger());

        $this->assertSame($templateCollectionMock, $this->fixture->getTemplates());
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     */
    public function testSettingAndGettingATarget() : void
    {
        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(__DIR__);

        $this->assertEquals(__DIR__, $this->fixture->getTarget());
    }

    /**
     * @covers ::setTarget
     */
    public function testExceptionWhenSettingFileAsTarget() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->setTarget(__FILE__);
    }

    /**
     * @covers ::setTarget
     */
    public function testExceptionWhenSettingExistingDirAsTarget() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Target directory (vfs://myroot) does not exist and could not be created');
        $fileSystem = vfsStream::setup('myroot');
        $this->fixture->setTarget(vfsStream::url('myroot'));
    }

    /**
     * @covers ::getTemplates
     */
    public function testRetrieveTemplateCollection() : void
    {
        $templateCollectionMock = m::mock('phpDocumentor\Transformer\Template\Collection');
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $writerCollectionMock->shouldIgnoreMissing();

        $fixture = new Transformer($templateCollectionMock, $writerCollectionMock, new NullLogger());

        $this->assertEquals($templateCollectionMock, $fixture->getTemplates());
    }

    /**
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $myTestWritter = 'myTestWriter';

        $templateCollection = m::mock('phpDocumentor\Transformer\Template\Collection');

        $project = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');

        $myTestWritterMock = m::mock('phpDocumentor\Transformer\Writer\WriterAbstract')
            ->shouldReceive('transform')->getMock();

        $writerCollectionMock = m::mock('phpDocumentor\Transformer\Writer\Collection')
            ->shouldReceive('offsetGet')->with($myTestWritter)->andReturn($myTestWritterMock)
            ->getMock();

        $fixture = new Transformer($templateCollection, $writerCollectionMock, new NullLogger());

        $transformation = m::mock('phpDocumentor\Transformer\Transformation')
            ->shouldReceive('execute')->with($project)
            ->shouldReceive('getQuery')->andReturn('')
            ->shouldReceive('getWriter')->andReturn($myTestWritter)
            ->shouldReceive('getArtifact')->andReturn('')
            ->shouldReceive('setTransformer')->with($fixture)
            ->getMock();

        $templateCollection->shouldReceive('getTransformations')->andReturn(
            [$transformation]
        );

        $fixture->execute($project);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription() : void
    {
        $description = $this->fixture->getDescription();
        $this->assertNotNull($description);
        $this->assertLessThanOrEqual(static::$MAX_DESCRIPTION_LENGTH, strlen($description));
    }
}
