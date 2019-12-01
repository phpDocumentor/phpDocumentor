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
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Reflection\Fqsen;
use function array_keys;
use function get_class;

/**
 * Tests the functionality for the Linker class.
 *
 * @coversDefaultClass \phpDocumentor\Compiler\Linker\Linker
 */
class LinkerTest extends MockeryTestCase
{
    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     * @covers ::isContextMarkerInFqsen()
     * @covers ::fetchElementByFqsen()
     * @covers ::getTypeWithClassAsContext()
     */
    public function testFindObjectAliasWithFqsenWhenContextIsClass() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyMethod()';
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');
        $container = m::mock(ClassDescriptor::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor\MyClass'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new Linker([]);
        $linker->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     * @covers ::isContextMarkerInFqsen()
     * @covers ::fetchElementByFqsen()
     * @covers ::getTypeWithNamespaceAsContext()
     */
    public function testFindObjectAliasWithFqsenAndContainerWhenContextIsContainerNamespace() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\phpDocumentor\Descriptor\MyClass';
        $container = m::mock(DescriptorAbstract::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new Linker([]);
        $linker->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     * @covers ::isContextMarkerInFqsen()
     * @covers ::fetchElementByFqsen()
     * @covers ::getTypeWithGlobalNamespaceAsContext()
     */
    public function testFindObjectAliasWithFqsenAndContainerWhenContextIsGlobalNamespace() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\MyClass';
        $container = m::mock(DescriptorAbstract::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new Linker([]);
        $linker->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::findAlias
     * @covers ::isContextMarkerInFqsen()
     * @covers ::fetchElementByFqsen()
     * @covers ::getTypeWithClassAsContext()
     * @covers ::getTypeWithNamespaceAsContext()
     * @covers ::getTypeWithGlobalNamespaceAsContext()
     */
    public function testFindObjectAliasReturnsNamespaceContextWhenElementIsUndocumented() : void
    {
        $fqsenWithContextMarker = '@context::MyClass';
        $container = m::mock(NamespaceDescriptor::class);
        $container
            ->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new Linker([]);

        $this->assertSame(
            '\phpDocumentor\Descriptor\MyClass',
            (string) $linker->findAlias($fqsenWithContextMarker, $container)
        );
    }

    /**
     * @covers ::findAlias
     */
    public function testFindObjectAliasReturnsNothingWithUnknownFqsen() : void
    {
        $linker = new Linker([]);

        $this->assertNull($linker->findAlias('\phpDocumentor\MyClass'));
    }

    /**
     * @covers ::findFieldValue
     */
    public function testFindFqsenInObject() : void
    {
        $fieldName = 'field';
        $fqsen = '\phpDocumentor\MyClass';

        $object = m::mock('stdClass');
        $object->shouldReceive('getField')->andReturn($fqsen);

        $linker = new Linker([]);

        $this->assertSame($fqsen, $linker->findFieldValue($object, $fieldName));
    }

    /**
     * @covers ::__construct
     * @covers ::getSubstitutions
     */
    public function testSetFieldsToSubstitute() : void
    {
        $elementList = [
            'phpDocumentor\Descriptor\ProjectDescriptor' => 'files',
            'phpDocumentor\Descriptor\FileDescriptor' => 'classes',
            'phpDocumentor\Descriptor\ClassDescriptor' => 'parent',
        ];
        $linker = new Linker($elementList);

        $this->assertSame($elementList, $linker->getSubstitutions());
    }

    /**
     * @covers ::__construct
     * @covers ::substitute
     * @covers ::isDescriptorContainer()
     */
    public function testSubstituteFqsenInObject() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $fieldName = 'field';

        [$object, $fqsen] = $this->createMockDescriptorForResult($result);

        // prepare linker
        $linker = new Linker([$fqsen => [$fieldName]]);
        $linker->setObjectAliasesList([$fqsen => $result]);

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::__construct
     * @covers ::substitute
     * @covers ::isDescriptorContainer()
     */
    public function testSubstituteFqsenInUnknownTypeDescriptor() : void
    {
        // initialize parameters
        $result = new ClassDescriptor();
        $fieldName = 'field';

        [$object, $fqsen] = $this->createMockUnknownTypeDescriptorForResult();

        // prepare linker
        $linker = new Linker([$fqsen => [$fieldName]]);
        $linker->setObjectAliasesList([$fqsen => $result]);

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::__construct
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
        $linker = new Linker([$fqsen => $fieldNames]);
        $linker->setObjectAliasesList([$fqsen => $result]);

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
        $linker = new Linker(
            [
                $fqsen => [$fieldName],
                $childFqsen => [$childFieldName],
            ]
        );
        $linker->setObjectAliasesList([$childFqsen => $result]);

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
        $linker = new Linker(
            [
                $fqsen => [$fieldName],
                $childFqsen => [$childFieldName],
            ]
        );
        $linker->setObjectAliasesList([$childFqsen => $result]);

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
        /** @var Linker|m\MockInterface $mock */
        $mock = m::mock('phpDocumentor\Compiler\Linker\Linker');
        $mock->shouldDeferMissing();
        $mock->shouldReceive('findAlias')->andReturn('substituted');
        $elementList = [
            'one' => ['two' => 'two'],
        ];
        $result = $mock->substitute($elementList);
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
        $mock = m::mock('phpDocumentor\Compiler\Linker\Linker');
        $mock->shouldDeferMissing();
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
        $linker = new Linker([]);
        $expected = 'Replace textual FQCNs with object aliases';
        $this->assertSame($expected, $linker->getDescription());
    }

    /**
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $indexes = new DescriptorCollection();
        $indexes->elements = new DescriptorCollection();
        $descriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $descriptor->shouldReceive('getIndexes')->andReturn($indexes);

        /** @var Linker|m\MockInterface $mock */
        $mock = m::mock(Linker::class);
        $mock->shouldDeferMissing();
        $mock->shouldReceive('substitute')->with($descriptor);
        $mock->execute($descriptor);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers ::execute
     * @covers ::replacePseudoTypes
     */
    public function testReplaceSelfWithCurrentClassInScope() : void
    {
        $fixture = new Linker(
            [
                'phpDocumentor\Descriptor\ClassDescriptor' => ['methods'],
                'phpDocumentor\Descriptor\MethodDescriptor' => ['tags'],
                'phpDocumentor\Descriptor\Tag\SeeDescriptor' => ['reference'],
            ]
        );

        $methodName = 'myMethod';
        $fqnn = '\My\Space';
        $className = 'MyClass';
        $seeDescriptor = $this->givenASeeDescriptorWithReference('self::' . $methodName . '()');
        $classDescriptor = $this->givenAClassWithNamespaceAndClassName($fqnn, $className);
        $methodDescriptor = $this->givenAMethodWithClassAndName($classDescriptor, $methodName);

        $methodDescriptor->getTags()->get($seeDescriptor->getName(), new Collection())->add($seeDescriptor);
        $classDescriptor->getMethods()->add($methodDescriptor);

        $fixture->setObjectAliasesList(
            [
                $fqnn . '\\' . $className => $classDescriptor,
                $fqnn . '\\' . $className . '::' . $methodName . '()' => $methodDescriptor,
            ]
        );

        $fixture->substitute($classDescriptor);

        $this->assertSame($methodDescriptor, $seeDescriptor->getReference());
    }

    /**
     * @covers ::execute
     * @covers ::replacePseudoTypes
     */
    public function testReplaceThisWithCurrentClassInScope() : void
    {
        $fixture = new Linker(
            [
                'phpDocumentor\Descriptor\ClassDescriptor' => ['methods'],
                'phpDocumentor\Descriptor\MethodDescriptor' => ['tags'],
                'phpDocumentor\Descriptor\Tag\SeeDescriptor' => ['reference'],
            ]
        );

        $methodName = 'myMethod';
        $fqnn = '\My\Space';
        $className = 'MyClass';
        $seeDescriptor = $this->givenASeeDescriptorWithReference('$this::' . $methodName . '()');
        $classDescriptor = $this->givenAClassWithNamespaceAndClassName($fqnn, $className);
        $methodDescriptor = $this->givenAMethodWithClassAndName($classDescriptor, $methodName);

        $methodDescriptor->getTags()->get($seeDescriptor->getName(), new Collection())->add($seeDescriptor);
        $classDescriptor->getMethods()->add($methodDescriptor);

        $fixture->setObjectAliasesList(
            [
                $fqnn . '\\' . $className => $classDescriptor,
                $fqnn . '\\' . $className . '::' . $methodName . '()' => $methodDescriptor,
            ]
        );

        $fixture->substitute($classDescriptor);

        $this->assertSame($methodDescriptor, $seeDescriptor->getReference());
    }

    /**
     * @return array
     */
    protected function createMockDescriptorForResult(?ClassDescriptor $result = null) : array
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

    /**
     * @return array
     */
    protected function createMockUnknownTypeDescriptorForResult() : array
    {
        $object = m::mock('phpDocumentor\Descriptor\Type\UnknownTypeDescriptor');
        $fqsen = get_class($object);

        $object->shouldReceive('getName')->andReturn('\Name');

        return [$object, $fqsen];
    }

    /**
     * Returns a ClassDescriptor whose namespace and name is set.
     */
    private function givenAClassWithNamespaceAndClassName(string $fqnn, string $className) : ClassDescriptor
    {
        $classDescriptor = new ClassDescriptor();
        $classDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($fqnn . '\\' . $className));
        $classDescriptor->setNamespace($fqnn);
        $classDescriptor->setName($className);

        return $classDescriptor;
    }

    /**
     * Returns a method whose name is set.
     */
    private function givenAMethodWithClassAndName(
        ClassDescriptor $classDescriptor,
        string $methodName
    ) : MethodDescriptor {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setName($methodName);
        $methodDescriptor->setFullyQualifiedStructuralElementName(
            new Fqsen($classDescriptor . '::' . $methodName . '()')
        );

        return $methodDescriptor;
    }

    /**
     * Returns a SeeDescriptor with its reference set.
     */
    private function givenASeeDescriptorWithReference(string $reference) : SeeDescriptor
    {
        $seeDescriptor = new SeeDescriptor('see');
        $seeDescriptor->setReference($reference);

        return $seeDescriptor;
    }
}
