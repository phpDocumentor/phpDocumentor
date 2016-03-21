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

namespace phpDocumentor\Application\ReadModel\Mappers\Project;

use Mockery as m;
use phpDocumentor\Reflection\Interpret;
use phpDocumentor\Reflection\InterpretInterface;
use phpDocumentor\Reflection\Interpreter;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File as FileType;
use phpDocumentor\Reflection\Php\Trait_ as TraitType;
use phpDocumentor\Reflection\Fqsen as FqsenType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class FileTest
 * @coversDefaultClass phpDocumentor\Application\ReadModel\Mappers\Project\FileTraits
 */
class FileTraitsTest extends \PHPUnit_Framework_TestCase
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
        $fileType->addTrait(new TraitType(new FqsenType('\MyTrait')));
        $context = new Context('Namespace');
        $command = new Interpret($fileType, $context);
        $reducerStub = new ReducerStub();

        $interpreter = new Interpreter(
            [
                new FileTraits(),
                $reducerStub
            ]
        );
        $command = $command->usingInterpreter($interpreter);

        $fileTraits = new FileTraits();

        $state = $fileTraits($command, null);

        $expectedState = ['traits' => ['MyTrait' => null]];

        $this->assertSame($reducerStub->isCalled, 2);
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
        $fileTraits = new FileTraits();

        $fileTraits($command, null);
    }
}
