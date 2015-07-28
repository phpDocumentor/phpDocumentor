<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Cli\Command;

use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass phpDocumentor\Transformer\Command\Template\ListCommand
 */
class ListCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::configure
     * @covers ::execute
     */
    public function testExecuteListsCommands()
    {
        // Arrange
        $command = new ListCommand($this->givenAFactoryWithTemplateNames(array('default', 'second')));

        $expectedOutput = <<<TXT
Available templates:
* default
* second


TXT;

        // Act
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(), array('decorated' => false));

        // Assert
        $this->assertSame($expectedOutput, $commandTester->getDisplay());
    }

    /**
     * Returns a factory mock object with the provided template names returned using the `getAllNames()` method.
     *
     * @param string[] $templateNames
     *
     * @return m\MockInterface
     */
    private function givenAFactoryWithTemplateNames(array $templateNames)
    {
        $factoryMock = m::mock('stdClass');
        $factoryMock->shouldReceive('getAllNames')->once()->andReturn($templateNames);

        return $factoryMock;
    }
}
