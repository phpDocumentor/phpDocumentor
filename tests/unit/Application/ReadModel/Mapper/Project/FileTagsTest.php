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
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File as FileType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class FileTest
 * @coversDefaultClass phpDocumentor\Application\ReadModel\Mapper\Project\FileTags
 */
class FileTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke()
     * @covers ::convertItems()
     * @covers ::convertItem()
     * @dataProvider generalDataProvider
     */
    public function testInterpretThisCommand($docBlock, $expected, $times)
    {
        $fileType = new FileType(
            'ecdc2862f54ccb495a53c469e83d45ff',
            'my/subdirectory/file.php',
            'the source of the file',
            $docBlock
        );
        $context = new Context('Namespace');
        $command = new Interpret($fileType, $context);
        $reducerStub = new ReducerStub();

        $interpreter = new Interpreter(
            [
                new FileTags(),
                $reducerStub
            ]
        );
        $command = $command->usingInterpreter($interpreter);

        $fileTags = new FileTags();

        $state = $fileTags($command, null);

        $this->assertSame($reducerStub->isCalled, $times);
        $this->assertSame($expected, $state);
    }

    /**
     * @covers ::__invoke()
     */
    public function testDontInterpretWithANonFileSubject()
    {
        $command = m::mock(InterpretInterface::class);
        $command->shouldReceive('subject')->once()->andReturn(new \stdClass());
        $command->shouldReceive('interpreter->next')->once()->with($command, null);
        $fileTags = new FileTags();

        $fileTags($command, null);
    }

    /**
     * Returns the array this reducer should produce
     * @return array
     */
    public function generalDataProvider()
    {
        return [
            [
                $docBlock = new DocBlock(
                    'Summary',
                    new DocBlock\Description('Description'),
                    [new DocBlock\Tags\Method('MyTag')]
                ),
                ['tags' => ['method' => null]],
                2
            ],
            [
                null,
                ['tags' => []],
                0
            ]
        ];
    }
}
