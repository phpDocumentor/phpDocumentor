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

namespace phpDocumentor\Compiler\Linker;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Prophecy\Argument;
use function array_keys;
use function get_class;

/**
 * Tests the functionality for the Linker class.
 *
 * @coversDefaultClass \phpDocumentor\Compiler\Linker\Linker
 * @covers ::__construct
 * @covers ::<private>
 */
final class LinkerTest extends MockeryTestCase
{
    /**
     * @covers ::getSubstitutions
     */
    public function testSetFieldsToSubstitute() : void
    {
        $elementList = [
            'phpDocumentor\Descriptor\ProjectDescriptor' => 'files',
            'phpDocumentor\Descriptor\FileDescriptor' => 'classes',
            'phpDocumentor\Descriptor\ClassDescriptor' => 'parent',
        ];
        $linker = new Linker($elementList, new DescriptorRepository());

        $this->assertSame($elementList, $linker->getSubstitutions());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteFqsenInObject() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $fieldName = 'field';

        [$object, $fqsen] = $this->createMockDescriptorForResult($result);

        // prepare linker
        $descriptorRepository = new DescriptorRepository();
        $descriptorRepository->setObjectAliasesList([$fqsen => $result]);
        $linker = new Linker([$fqsen => [$fieldName]], $descriptorRepository);

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testMultipleSubstitutionsInOneObject() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $fieldNames = ['field1', 'field2'];

        // assert that the getField is called (and returns a FQSEN) and the setField is called with the expected object
        $object = m::mock(ClassDescriptor::class);
        $fqsen = get_class($object);
        foreach (array_keys($fieldNames) as $index) {
            $object->shouldReceive('getField' . ($index + 1))->atLeast()->once()->andReturn($fqsen);
            $object->shouldReceive('setField' . ($index + 1))->atLeast()->once()->with($result);
        }

        // prepare linker
        $descriptorRepository = new DescriptorRepository();
        $descriptorRepository->setObjectAliasesList([$fqsen => $result]);
        $linker = new Linker([$fqsen => $fieldNames], $descriptorRepository);

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testSubstituteFieldsViaChildObject() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $childFieldName = 'field';
        $fieldName = 'child';

        [$childObject, $childFqsen] = $this->createMockDescriptorForResult($result);

        $object = m::mock('phpDocumentor\Descripto\DescriptorAbstract');
        $fqsen = get_class($object);
        $object->shouldReceive('getChild')->atLeast()->once()->andReturn($childObject);
        $object->shouldReceive('setChild')->never();

        // prepare linker
        $descriptorRepository = new DescriptorRepository();
        $descriptorRepository->setObjectAliasesList([$childFqsen => $result]);
        $linker = new Linker(
            [
                $fqsen => [$fieldName],
                $childFqsen => [$childFieldName],
            ],
            $descriptorRepository
        );

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testSubstituteFieldsViaArrayOfChildObjects() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $childFieldName = 'field';
        $fieldName = 'child';

        [$childObject, $childFqsen] = $this->createMockDescriptorForResult($result);

        $object = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $fqsen = get_class($object);
        $object->shouldReceive('getChild')->atLeast()->once()->andReturn([$childObject]);
        $object->shouldReceive('setChild');

        // prepare linker
        $descriptorRepository = new DescriptorRepository();
        $descriptorRepository->setObjectAliasesList([$childFqsen => $result]);
        $linker = new Linker(
            [
                $fqsen => [$fieldName],
                $childFqsen => [$childFieldName],
            ],
            $descriptorRepository
        );

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteArrayRecursive() : void
    {
        $repository = $this->prophesize(DescriptorRepository::class);
        $linker = new Linker([], $repository->reveal());
        $repository->findAlias(Argument::cetera())->willReturn('substituted');
        $elementList = [
            'one' => ['two' => 'two'],
        ];
        $result = $linker->substitute($elementList);
        $expected = [
            'one' => ['two' => 'substituted'],
        ];
        $this->assertSame($expected, $result);
    }

    /**
     * Test that already processed objects don't substitute again
     * Using mockery, as return value would be `null` in both cases
     *
     * @covers ::substitute
     */
    public function testSubstituteSkipProcessed() : void
    {
        /** @var Linker|m\MockInterface $mock */
        $mock = m::mock(Linker::class);
        $mock->makePartial();
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('findFieldValue')->atMost()->once();

        $item = new ClassDescriptor();
        $item->attribute = 'foreachme';

        //findFieldValue() should be called
        $result = $mock->substitute($item);

        //findFieldvalue() should NOT be called
        $result = $mock->substitute($item);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription() : void
    {
        $linker = new Linker([], new DescriptorRepository());
        $expected = 'Replace textual FQCNs with object aliases';
        $this->assertSame($expected, $linker->getDescription());
    }

    /**
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $result = new ClassDescriptor();
        $object = $this->prophesize(ClassDescriptor::class);
        $fqsen = get_class($object);

        $project = new ProjectDescriptor('project');
        $project->setIndexes(new Collection(['elements' => new Collection([$fqsen => $result])]));

        // prepare linker
        $repository = $this->prophesize(DescriptorRepository::class);
        $repository->setObjectAliasesList([$fqsen => $result])->shouldBeCalledOnce();
        $linker = new Linker([$fqsen => ['field']], $repository->reveal());

        // execute test.
        $linker->execute($project);
    }

    /**
     * @covers ::execute
     */
    private function createMockDescriptorForResult(?ClassDescriptor $result = null) : array
    {
        $object = m::mock(ClassDescriptor::class);
        $fqsen = get_class($object);
        $object->shouldReceive('getField')->atLeast()->once()->andReturn($fqsen);

        if ($result) {
            $object->shouldReceive('setField')->atLeast()->once()->with($result);
        } else {
            $object->shouldReceive('setField')->never();
        }

        return [$object, $fqsen];
    }
}
