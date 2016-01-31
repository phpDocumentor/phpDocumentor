<?php

namespace phpDocumentor\Renderer\Action;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\Renderer\RenderPass;
use Mockery as m;
use phpDocumentor\Renderer\Template\Parameter;

/**
 * @coversDefaultClass phpDocumentor\Renderer\Action\CopyFile
 * @covers ::<private>
 */
final class CopyFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create
     * @covers ::getRenderPass
     * @covers ::getSource
     * @covers ::getDestination
     */
    public function testCreateNewCommand()
    {
        $renderPass = m::mock(RenderPass::class);
        $source = '123';
        $destination = '1234';

        $command = CopyFile::create(
            [
                'renderPass' => new Parameter('renderPass', $renderPass),
                'source' => new Parameter('source', $source),
                'destination' => new Parameter('destination', $destination)
            ]
        );

        $this->assertSame($renderPass, $command->getRenderPass());
        $this->assertInstanceOf(Path::class, $command->getSource());
        $this->assertSame($source, (string)$command->getSource());
        $this->assertInstanceOf(Path::class, $command->getDestination());
        $this->assertSame($destination, (string)$command->getDestination());
    }

    /**
     * @covers ::__toString
     */
    public function testExtractLogLine()
    {
        $renderPass = m::mock(RenderPass::class);
        $source = '123';
        $destination = '1234';

        $command = CopyFile::create(
            [
                'renderPass' => new Parameter('renderPass', $renderPass),
                'source' => new Parameter('source', $source),
                'destination' => new Parameter('destination', $destination)
            ]
        );

        $this->assertSame('Copied file 123 to 1234', (string)$command);
    }

    /**
     * @covers ::create
     * @dataProvider provideMissingParameterPermutations
     */
    public function testErrorIsThrownIfParameterIsMissing($permutation)
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        CopyFile::create($permutation);
    }

    public function provideMissingParameterPermutations()
    {
        $renderPass = new Parameter('renderPass', m::mock(RenderPass::class));
        $source = new Parameter('source', '123');
        $destination = new Parameter('destination', '1234');

        return [
            [ [ 'source' => $source, 'destination' => $destination ] ],
            [ [ 'renderPass' => $renderPass, 'destination' => $destination ] ],
            [ [ 'renderPass' => $renderPass, 'source' => $source, ] ],
        ];
    }
}
