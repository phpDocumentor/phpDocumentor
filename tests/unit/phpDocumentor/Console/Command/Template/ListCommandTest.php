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
namespace phpDocumentor\Console\Command\Template;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Transformer\Template\Factory;
use Symfony\Component\Console\Tester\CommandTester;
use const PHP_EOL;
use function str_replace;

/**
 * @coversDefaultClass \phpDocumentor\Console\Command\Template\ListCommand
 */
class ListCommandTest extends MockeryTestCase
{
    /**
     * @covers ::__construct
     * @covers ::configure
     * @covers ::execute
     */
    public function testExecuteListsCommands() : void
    {
        // Arrange
        $command = new ListCommand($this->givenAFactoryWithTemplateNames(['default', 'second']));

        $expectedOutput = <<<TXT
Available templates:
* default
* second


TXT;
        $expectedOutput = str_replace("\n", PHP_EOL, $expectedOutput);

        // Act
        $commandTester = new CommandTester($command);
        $commandTester->execute([], ['decorated' => false]);

        // Assert
        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }

    /**
     * Returns a factory mock object with the provided template names returned using the `getAllNames()` method.
     *
     * @param string[] $templateNames
     *
     * @return m\MockInterface|Factory
     */
    private function givenAFactoryWithTemplateNames(array $templateNames)
    {
        $factoryMock = m::mock('phpDocumentor\Transformer\Template\Factory');
        $factoryMock->shouldReceive('getAllNames')->once()->andReturn($templateNames);

        return $factoryMock;
    }
}
