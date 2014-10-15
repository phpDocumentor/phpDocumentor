<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Command\Template;

use Mockery as m;
use phpDocumentor\Transformer\Template\Factory;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Command\Template\ListCommand::__construct
     * @covers phpDocumentor\Transformer\Command\Template\ListCommand::configure
     * @covers phpDocumentor\Transformer\Command\Template\ListCommand::execute
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
     * @return m\MockInterface|Factory
     */
    private function givenAFactoryWithTemplateNames(array $templateNames)
    {
        $factoryMock = m::mock('phpDocumentor\Transformer\Template\Factory');
        $factoryMock->shouldReceive('getAllNames')->once()->andReturn($templateNames);

        return $factoryMock;
    }
}
