<?php

namespace phpDocumentor\Renderer\Action;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Template\Action\Xml;
use phpDocumentor\DomainModel\Template\RenderPass;
use Mockery as m;
use phpDocumentor\DomainModel\Template\Parameter;

/**
 * @coversDefaultClass phpDocumentor\Renderer\Action\Xml
 * @covers ::<private>
 */
final class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create
     * @covers ::getRenderPass
     * @covers ::getDestination
     */
    public function testCreateNewCommand()
    {
        $renderPass = m::mock(RenderPass::class);
        $destination = '1234';

        $command = Xml::create(
            [
                'renderPass' => new Parameter('renderPass', $renderPass),
                'destination' => new Parameter('destination', $destination)
            ]
        );

        $this->assertSame($renderPass, $command->getRenderPass());
        $this->assertInstanceOf(Path::class, $command->getDestination());
        $this->assertSame($destination, (string)$command->getDestination());
    }

    /**
     * @covers ::create
     * @covers ::getRenderPass
     * @covers ::getDestination
     */
    public function testCreateNewCommandUsingBackwardsCompatibleOptions()
    {
        $renderPass = m::mock(RenderPass::class);
        $destination = '1234';

        $command = Xml::create(
            [
                'renderPass' => new Parameter('renderPass', $renderPass),
                'artifact' => new Parameter('artifact', $destination)
            ]
        );

        $this->assertSame($renderPass, $command->getRenderPass());
        $this->assertInstanceOf(Path::class, $command->getDestination());
        $this->assertSame($destination, (string)$command->getDestination());
    }

    /**
     * @covers ::__toString
     */
    public function testExtractLogLine()
    {
        $renderPass = m::mock(RenderPass::class);
        $destination = '1234';

        $command = Xml::create(
            [
                'renderPass' => new Parameter('renderPass', $renderPass),
                'destination' => new Parameter('destination', $destination)
            ]
        );

        $this->assertSame('Rendered XML structure at "1234"', (string)$command);
    }

    /**
     * @covers ::create
     * @dataProvider provideMissingParameterPermutations
     */
    public function testErrorIsThrownIfParameterIsMissing($permutation)
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        Xml::create($permutation);
    }

    public function provideMissingParameterPermutations()
    {
        $renderPass = new Parameter('renderPass', m::mock(RenderPass::class));
        $destination = new Parameter('destination', '1234');

        return [
            [ [ 'destination' => $destination ] ],
            [ [ 'renderPass' => $renderPass ] ],
        ];
    }
}
