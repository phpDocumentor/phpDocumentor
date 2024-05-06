<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Validation\Error;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\FileDescriptor
 * @covers ::__construct
 * @covers \phpDocumentor\Descriptor\DescriptorAbstract
 */
final class FileDescriptorTest extends MockeryTestCase
{
    private const EXAMPLE_HASH = 'a-hash-string';

    private const EXAMPLE_PATH = 'a-path-string';

    private const EXAMPLE_SOURCE = 'a-source-string';

    private FileDescriptor $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new FileDescriptor(self::EXAMPLE_HASH);
    }

    /**
     * Tests whether all collection objects and hash are properly initialized
     *
     * @covers ::__construct
     */
    public function testInitializesWithEmptyCollections(): void
    {
        self::assertSame(self::EXAMPLE_HASH, $this->fixture->getHash());

        self::assertInstanceOf(Collection::class, $this->fixture->getNamespaceAliases());
        self::assertInstanceOf(Collection::class, $this->fixture->getIncludes());
        self::assertInstanceOf(Collection::class, $this->fixture->getConstants());
        self::assertInstanceOf(Collection::class, $this->fixture->getFunctions());
        self::assertInstanceOf(Collection::class, $this->fixture->getClasses());
        self::assertInstanceOf(Collection::class, $this->fixture->getInterfaces());
        self::assertInstanceOf(Collection::class, $this->fixture->getTraits());
        self::assertInstanceOf(Collection::class, $this->fixture->getEnums());
        self::assertInstanceOf(Collection::class, $this->fixture->getMarkers());
    }

    /**
     * @covers ::__construct
     * @covers ::getHash
     */
    public function testGetHash(): void
    {
        self::assertSame(self::EXAMPLE_HASH, $this->fixture->getHash());
    }

    /**
     * @covers ::setPath
     * @covers ::getPath
     */
    public function testSetAndGetPath(): void
    {
        self::assertSame('', $this->fixture->getPath());

        $this->fixture->setPath(self::EXAMPLE_PATH);

        self::assertSame(self::EXAMPLE_PATH, $this->fixture->getPath());
    }

    /**
     * @covers ::setSource
     * @covers ::getSource
     */
    public function testSetAndGetSource(): void
    {
        self::assertNull($this->fixture->getSource());

        $this->fixture->setSource(self::EXAMPLE_SOURCE);

        self::assertSame(self::EXAMPLE_SOURCE, $this->fixture->getSource());
    }

    /**
     * @covers ::setNamespaceAliases
     * @covers ::getNamespaceAliases
     */
    public function testSetAndGetNamespaceAliases(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getNamespaceAliases());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setNamespaceAliases($mock);

        self::assertSame($mockInstance, $this->fixture->getNamespaceAliases());
    }

    /**
     * @covers ::setIncludes
     * @covers ::getIncludes
     */
    public function testSetAndGetIncludes(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getIncludes());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setIncludes($mock);

        self::assertSame($mockInstance, $this->fixture->getIncludes());
    }

    /**
     * @covers ::setConstants
     * @covers ::getConstants
     */
    public function testSetAndGetConstants(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setConstants($mock);

        self::assertSame($mockInstance, $this->fixture->getConstants());
    }

    /**
     * @covers ::setFunctions
     * @covers ::getFunctions
     */
    public function testSetAndGetFunctions(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getFunctions());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setFunctions($mock);

        self::assertSame($mockInstance, $this->fixture->getFunctions());
    }

    /**
     * @covers ::setClasses
     * @covers ::getClasses
     */
    public function testSetAndGetClasses(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getClasses());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setClasses($mock);

        self::assertSame($mockInstance, $this->fixture->getClasses());
    }

    /**
     * @covers ::setInterfaces
     * @covers ::getInterfaces
     */
    public function testSetAndGetInterfaces(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setInterfaces($mock);

        self::assertSame($mockInstance, $this->fixture->getInterfaces());
    }

    /**
     * @covers ::setTraits
     * @covers ::getTraits
     */
    public function testSetAndGetTraits(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getTraits());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setTraits($mock);

        self::assertSame($mockInstance, $this->fixture->getTraits());
    }

    /**
     * @covers ::setEnums
     * @covers ::getEnums
     */
    public function testSetAndGetEnums(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getEnums());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setEnums($mock);

        self::assertSame($mockInstance, $this->fixture->getEnums());
    }

    /**
     * @covers ::setMarkers
     * @covers ::getMarkers
     */
    public function testSetAndGetMarkers(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getMarkers());

        $mockInstance = m::mock(Collection::class);
        $mock = $mockInstance;

        $this->fixture->setMarkers($mock);

        self::assertSame($mockInstance, $this->fixture->getMarkers());
    }

    /**
     * @covers ::__construct
     * @covers ::getAllErrors
     */
    public function testIfErrorsAreInitializedToAnEmptyCollectionOnInstantiation(): void
    {
        // construct
        self::assertInstanceOf(Collection::class, $this->fixture->getAllErrors());

        // default returns empty array
        self::assertObjectHasProperty('items', $this->fixture->getAllErrors());

        $items = $this->fixture->getAllErrors()->getAll();
        self::assertEmpty($items);
    }

    /**
     * @covers ::__construct
     * @covers ::getAllErrors
     * @todo Split this test into its various scenario's instead of one big lump
     */
    public function testGetAllErrors(): void
    {
        /*
         * constant
         * function
         * class
         *     property
         *     constant
         *     method
         * interface
         *     constant
         *     method
         * traits
         *     property
         *     method
         */

        // setup error list
        $errorGlobal = [new Error('error', 'error-global', 10)];
        $errorClasses = [new Error('error', 'error-class', 10)];
        $errorClassMethods = [new Error('error', 'error-class-method', 10)];
        $errorClassConstants = [new Error('error', 'error-class-constant', 10)];
        $errorClassProperties = [new Error('error', 'error-class-property', 10)];
        $errorInterfaces = [new Error('error', 'error-interface', 10)];
        $errorInterfacesConstants = [new Error('error', 'error-interface-constant', 10)];
        $errorInterfacesMethods = [new Error('error', 'error-interface-method', 10)];
        $errorTraits = [new Error('error', 'error-traits', 10)];
        $errorTraitsProperties = [new Error('error', 'error-traits-property', 10)];
        $errorTraitsMethods = [new Error('error', 'error-traits-method', 10)];
        $errorFunctions = [new Error('error', 'error-functions', 10)];

        // setup global check
        $collection = new Collection($errorGlobal);
        $this->fixture->setErrors($collection);

        // setup class-property check
        $mockClassProperties = m::mock(PropertyDescriptor::class);
        $mockClassProperties->shouldReceive('getErrors')->andReturn(new Collection($errorClassProperties));

        // setup class-constant check
        $mockClassConstants = m::mock(ConstantDescriptor::class);
        $mockClassConstants->shouldReceive('getErrors')->andReturn(new Collection($errorClassConstants));

        // setup class-method check
        $mockClassMethods = m::mock(MethodDescriptor::class);
        $mockClassMethods->shouldReceive('getErrors')->andReturn(new Collection($errorClassMethods));

        // setup class check
        $mockClasses = m::mock(ClassDescriptor::class);
        $mockClasses->shouldReceive('getProperties')->andReturn(new Collection([$mockClassProperties]));
        $mockClasses->shouldReceive('getConstants')->andReturn(new Collection([$mockClassConstants]));
        $mockClasses->shouldReceive('getMethods')->andReturn(new Collection([$mockClassMethods]));
        $mockClasses->shouldReceive('getErrors')->andReturn(new Collection($errorClasses));

        $this->fixture->getClasses()->set('my-test-class', $mockClasses);

        // setup interface-constant check
        $mockInterfaceConstants = m::mock(ConstantDescriptor::class);
        $mockInterfaceConstants->shouldReceive('getErrors')->andReturn(new Collection($errorInterfacesConstants));

        // setup interface-method check
        $mockInterfaceMethods = m::mock(MethodDescriptor::class);
        $mockInterfaceMethods->shouldReceive('getErrors')->andReturn(new Collection($errorInterfacesMethods));

        // setup interface check
        $mockInterfaces = m::mock(ClassDescriptor::class);
        $mockInterfaces->shouldReceive('getProperties')->andReturn(new Collection());
        $mockInterfaces->shouldReceive('getConstants')->andReturn(new Collection([$mockInterfaceConstants]));
        $mockInterfaces->shouldReceive('getMethods')->andReturn(new Collection([$mockInterfaceMethods]));
        $mockInterfaces->shouldReceive('getErrors')->andReturn(new Collection($errorInterfaces));

        $this->fixture->getClasses()->set('my-test-interface', $mockInterfaces);

        // setup traits-constant check
        $mockTraitsProperties = m::mock(ConstantDescriptor::class);
        $mockTraitsProperties->shouldReceive('getErrors')->andReturn(new Collection($errorTraitsProperties));

        // setup traits-method check
        $mockTraitsMethods = m::mock(MethodDescriptor::class);
        $mockTraitsMethods->shouldReceive('getErrors')->andReturn(new Collection($errorTraitsMethods));

        // setup traits check
        $mockTraits = m::mock(ClassDescriptor::class);
        $mockTraits->shouldReceive('getConstants')->andReturn(new Collection());
        $mockTraits->shouldReceive('getProperties')->andReturn(new Collection([$mockTraitsProperties]));
        $mockTraits->shouldReceive('getMethods')->andReturn(new Collection([$mockTraitsMethods]));
        $mockTraits->shouldReceive('getErrors')->andReturn(new Collection($errorTraits));

        $this->fixture->getClasses()->set('my-test-traits', $mockTraits);

        // setup functions check
        $mockFunctions = m::mock(FunctionDescriptor::class);

        // create dummy instances of constants/methods
        $mockFunctions->shouldReceive('getConstants')->andReturn(new Collection());
        $mockFunctions->shouldReceive('getProperties')->andReturn(new Collection());
        $mockFunctions->shouldReceive('getMethods')->andReturn(new Collection());
        $mockFunctions->shouldReceive('getErrors')->andReturn(new Collection($errorFunctions));

        $this->fixture->getClasses()->set('my-test-function', $mockFunctions);

        // final merge and check
        $expectedErrors = [
            ...$errorTraitsProperties,
            ...$errorTraitsMethods,
            ...$errorInterfacesConstants,
            ...$errorInterfacesMethods,
            ...$errorClassProperties,
            ...$errorClassConstants,
            ...$errorClassMethods,
            ...$errorFunctions,
            ...$errorTraits,
            ...$errorInterfaces,
            ...$errorClasses,
            ...$errorGlobal,
        ];

        self::assertSame($expectedErrors, $this->fixture->getAllErrors()->getAll());
    }
}
