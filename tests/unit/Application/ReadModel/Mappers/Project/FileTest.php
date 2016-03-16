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
use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Reducer;
use phpDocumentor\Reflection\Interpret;
use phpDocumentor\Reflection\InterpretInterface;
use phpDocumentor\Reflection\Interpreter;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File as FileType;
use phpDocumentor\Reflection\Fqsen as FqsenType;
use phpDocumentor\Reflection\Php\Constant as ConstantType;
use phpDocumentor\Reflection\Php\Function_ as FunctionType;
use phpDocumentor\Reflection\Php\Class_ as ClassType;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceType;
use phpDocumentor\Reflection\Php\Trait_ as TraitType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class FileTest
 * @coversDefaultClass phpDocumentor\Application\ReadModel\Mappers\Project\File
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__invoke()
     */
    public function it_should_interpret_this_command()
    {
        $docBlock = new DocBlock(
            'Summary',
            new DocBlock\Description('Description'),
            [new DocBlock\Tags\Method('MyTag')]
        );
        $fileType = new FileType(
            'ecdc2862f54ccb495a53c469e83d45ff',
            'my/subdirectory/file.php',
            'the source of the file',
            $docBlock
        );
        $fileType->addNamespace(new FqsenType('\Namespace\Class'));
        $fileType->addInclude('MyInclude.php');
        $fileType->addConstant(new ConstantType(new FqsenType('\namespace\class::myConstant')));
        $fileType->addFunction(new FunctionType(new FqsenType('\MyFunction')));
        $fileType->addClass(new ClassType(new FqsenType('\MyClass')));
        $fileType->addInterface(new InterfaceType(new FqsenType('\MyInterface')));
        $fileType->addTrait(new TraitType(new FqsenType('\MyTrait')));
        $context = new Context('Namespace');
        $command = new Interpret($fileType, $context);

        $reducer = m::mock(Reducer::class);
        $reducer->shouldReceive('__invoke')
            ->andReturn(
                ['constant'],
                ['function'],
                ['class'],
                ['interface'],
                ['trait'],
                ['tag']
            )
            ->globally()
            ->ordered();

        $interpreter = new Interpreter(
            [
                new File(),
                $reducer,
                $reducer,
                $reducer,
                $reducer,
                $reducer,
                $reducer
            ]
        );
        $command = $command->usingInterpreter($interpreter);

        $file = new File();

        $state = $file($command, null);

        $this->assertSame($this->expected(), $state);
    }

    /**
     * @test
     * @covers ::__invoke()
     */
    public function it_should_not_interpret_a_command_with_a_non_file_subject()
    {
        $command = m::mock(InterpretInterface::class);
        $command->shouldReceive('subject')->once()->andReturn(new \stdClass());
        $command->shouldReceive('interpreter->next')->once()->with($command, null);
        $file = new File();

        $file($command, null);
    }

    /**
     * Returns the array this reducer should produce
     * @return array
     */
    private function expected()
    {
        return [
            'hash' => 'ecdc2862f54ccb495a53c469e83d45ff',
            'path' => 'my/subdirectory/file.php',
            'source' => 'the source of the file',
            'namespaceAliases' => [],
            'includes' => ['MyInclude.php' => 'MyInclude.php'],
            'constants' => ['myConstant' => ['constant']],
            'functions' => ['MyFunction' => ['function']],
            'classes' => ['MyClass' => ['class']],
            'interfaces' => ['MyInterface' => ['interface']],
            'traits' => ['MyTrait' => ['trait']],
            'markers' => [],
            'fqsen' => '',
            'name' => 'file.php',
            'namespace' => 'Namespace',
            'package' => '',
            'summary' => 'Summary',
            'description' => 'Description',
            'filedescriptor' => null,
            'line' => 0,
            'tags' => ['method' => ['tag']],
            'errors' => '',
            'inheritedElement' => null
        ];
    }
}
