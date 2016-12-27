<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\ReadModel\Mapper\Project;

use Mockery as m;
use phpDocumentor\Application\ReadModel\Mapper\Project\Interpret;
use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Interpret as InterpretInterface;
use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Interpreter;
use phpDocumentor\Reflection\Php\File as FileType;
use phpDocumentor\Reflection\Php\Function_ as FunctionType;
use phpDocumentor\Reflection\Fqsen as FqsenType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class FileTest
 * @coversDefaultClass phpDocumentor\Application\ReadModel\Mapper\Project\FileFunctions
 */
class FileFunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke()
     * @covers ::convertItems()
     * @covers ::convertItem()
     */
    public function testInterpretThisCommand()
    {
        $fileType = new FileType(
            'ecdc2862f54ccb495a53c469e83d45ff',
            'my/subdirectory/file.php',
            'the source of the file'
        );
        $fileType->addFunction(new FunctionType(new FqsenType('\MyFunction')));
        $context = new Context('Namespace');
        $command = new Interpret($fileType, $context);
        $reducerStub = new ReducerStub();

        $interpreter = new Interpreter(
            [
                new FileFunctions(),
                $reducerStub
            ]
        );
        $command = $command->usingInterpreter($interpreter);

        $fileFunctions = new FileFunctions();

        $state = $fileFunctions($command, null);

        $expectedState = ['functions' => ['MyFunction' => null]];

        $this->assertSame($reducerStub->isCalled, 3);
        $this->assertSame($expectedState, $state);
    }

    /**
     * @covers ::__invoke()
     */
    public function testDontInterpretWithANonFileSubject()
    {
        $command = m::mock(InterpretInterface::class);
        $command->shouldReceive('subject')->once()->andReturn(new \stdClass());
        $command->shouldReceive('interpreter->next')->once()->with($command, null);
        $fileFunctions = new FileFunctions();

        $fileFunctions($command, null);
    }
}
