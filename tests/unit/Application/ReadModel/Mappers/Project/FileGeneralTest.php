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
use phpDocumentor\Reflection\Fqsen as FqsenType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class FileTest
 * @coversDefaultClass phpDocumentor\Application\ReadModel\Mappers\Project\FileGeneral
 */
class FileGeneralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke()
     * @dataProvider generalDataProvider
     */
    public function testInterpretThisCommand($description, $expected)
    {
        $docBlock = $description;
        $fileType = new FileType(
            'ecdc2862f54ccb495a53c469e83d45ff',
            'my/subdirectory/file.php',
            'the source of the file',
            $docBlock
        );
        $fileType->addNamespace(new FqsenType('\Namespace\Class'));
        $fileType->addInclude('MyInclude.php');
        $context = new Context('Namespace');
        $command = new Interpret($fileType, $context);
        $reducerStub = new ReducerStub();

        $interpreter = new Interpreter(
            [
                new FileGeneral(),
                $reducerStub
            ]
        );
        $command = $command->usingInterpreter($interpreter);

        $fileProps = new FileGeneral();

        $state = $fileProps($command, null);

        $this->assertSame($expected, $state);
        $this->assertSame($reducerStub->isCalled, 1);
    }
    /**
     * @covers ::__invoke()
     */
    public function testDontInterpretWithANonFileSubject()
    {
        $command = m::mock(InterpretInterface::class);
        $command->shouldReceive('subject')->once()->andReturn(new \stdClass());
        $command->shouldReceive('interpreter->next')->once()->with($command, null);
        $fileProps = new FileGeneral();

        $fileProps($command, null);
    }

    /**
     * Returns the array this reducer should produce
     * @return array
     */
    public function generalDataProvider()
    {
        return [
            [
                new DocBlock(
                    'Summary',
                    new DocBlock\Description('Description')
                ),
                [
                    'hash' => 'ecdc2862f54ccb495a53c469e83d45ff',
                    'path' => 'my/subdirectory/file.php',
                    'source' => 'the source of the file',
                    'namespaceAliases' => [],
                    'includes' => ['MyInclude.php' => 'MyInclude.php'],
                    'markers' => [],
                    'fqsen' => '',
                    'name' => 'file.php',
                    'namespace' => 'Namespace',
                    'package' => '',
                    'summary' => 'Summary',
                    'description' => 'Description',
                    'filedescriptor' => null,
                    'line' => 0,
                    'errors' => '',
                    'inheritedElement' => null
                ]
            ],
            [
                null,
                [
                    'hash' => 'ecdc2862f54ccb495a53c469e83d45ff',
                    'path' => 'my/subdirectory/file.php',
                    'source' => 'the source of the file',
                    'namespaceAliases' => [],
                    'includes' => ['MyInclude.php' => 'MyInclude.php'],
                    'markers' => [],
                    'fqsen' => '',
                    'name' => 'file.php',
                    'namespace' => 'Namespace',
                    'package' => '',
                    'summary' => '',
                    'description' => '',
                    'filedescriptor' => null,
                    'line' => 0,
                    'errors' => '',
                    'inheritedElement' => null
                ]
            ]
        ];
    }
}
