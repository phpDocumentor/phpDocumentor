<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mvriel
 * Date: 2/1/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */

namespace phpDocumentor\Descriptor\Builder\Test;

use Mockery as m;
use phpDocumentor\Descriptor\Builder\Reflector;

/**
 * Test class for \phpDocumentor\Parser\Parser.
 *
 * @covers phpDocumentor\Descriptor\Builder\Reflector
 */
class ReflectorTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildFile()
    {
        // FIXME
        $this->markTestIncomplete('To be fixed');
        $path = '/my/path.txt';

        $reflector = $this->createFileReflectorMock($path);

        $test = $this;
        $file_descriptor_test = m::on(function($file_descriptor) use ($path, $test) {
            $test->assertInstanceOf('phpDocumentor\Descriptor\FileDescriptor', $file_descriptor);
            $test->assertEquals($path, $file_descriptor->getPath());
            $test->assertEquals(0, $file_descriptor->getLine());
            $test->assertEquals('path.txt', $file_descriptor->getName());
            $test->assertEquals('short', $file_descriptor->getSummary());
            $test->assertEquals('long', $file_descriptor->getDescription());
            $test->assertCount(1, $file_descriptor->getTags());
            $test->assertEquals('contents', $file_descriptor->getSource());
            return true;
        });

        $project = m::mock('phpDocumentor\Descriptor\ProjectDescriptor')
            ->shouldReceive('getFiles')->andReturn(
                m::mock('ArrayObject')->shouldReceive('offsetSet')->once()->with($path, $file_descriptor_test)->getMock()
            )
            ->getMock();

        $fixture = new Reflector($project);
        $fixture->buildFile($reflector);
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function createFileReflectorMock($path)
    {
        // tag, but can't override the tag class itself
        $tagMock = m::mock('phpDocumentor\Reflection\DocBlock\Tag')
            ->shouldReceive('getName')->andReturn('name')
            ->shouldReceive('getDescription')->andReturn('description')
            ->getMock();

        $reflector = m::mock('phpDocumentor\Reflection\FileReflector')
            ->shouldReceive('getFilename')->andReturn($path)
            ->shouldReceive('getContents')->andReturn('contents')
            ->shouldReceive('getDocBlock')->andReturn(
                m::mock('phpDocumentor\Reflection\DocBlock')
                    ->shouldReceive('getShortDescription')->andReturn('short')
                    ->shouldReceive('getLongDescription')->andReturn(
                        m::mock('phpDocumentor\Reflection\DocBlock\Description')
                            ->shouldReceive('getContents')->andReturn('long')
                            ->getMock()
                    )
                    ->shouldReceive('getTags')->andReturn(array($tagMock))
                    ->getMock()
            )
            ->getMock();
        $reflector->shouldIgnoreMissing();
        return $reflector;
    }
}
