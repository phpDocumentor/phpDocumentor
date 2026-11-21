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

use phpDocumentor\Descriptor\Validation\Error;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileDescriptor::class)]
#[CoversClass(DescriptorAbstract::class)]
final class FileDescriptorTest extends TestCase
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

    public function testGetHash(): void
    {
        self::assertSame(self::EXAMPLE_HASH, $this->fixture->getHash());
    }

    public function testSetAndGetPath(): void
    {
        self::assertSame('', $this->fixture->getPath());

        $this->fixture->setPath(self::EXAMPLE_PATH);

        self::assertSame(self::EXAMPLE_PATH, $this->fixture->getPath());
    }

    public function testSetAndGetSource(): void
    {
        self::assertNull($this->fixture->getSource());

        $this->fixture->setSource(self::EXAMPLE_SOURCE);

        self::assertSame(self::EXAMPLE_SOURCE, $this->fixture->getSource());
    }

    public function testSetAndGetNamespaceAliases(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getNamespaceAliases());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setNamespaceAliases($mock);

        self::assertSame($mockInstance, $this->fixture->getNamespaceAliases());
    }

    public function testSetAndGetIncludes(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getIncludes());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setIncludes($mock);

        self::assertSame($mockInstance, $this->fixture->getIncludes());
    }

    public function testSetAndGetConstants(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setConstants($mock);

        self::assertSame($mockInstance, $this->fixture->getConstants());
    }

    public function testSetAndGetFunctions(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getFunctions());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setFunctions($mock);

        self::assertSame($mockInstance, $this->fixture->getFunctions());
    }

    public function testSetAndGetClasses(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getClasses());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setClasses($mock);

        self::assertSame($mockInstance, $this->fixture->getClasses());
    }

    public function testSetAndGetInterfaces(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setInterfaces($mock);

        self::assertSame($mockInstance, $this->fixture->getInterfaces());
    }

    public function testSetAndGetTraits(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getTraits());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setTraits($mock);

        self::assertSame($mockInstance, $this->fixture->getTraits());
    }

    public function testSetAndGetEnums(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getEnums());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setEnums($mock);

        self::assertSame($mockInstance, $this->fixture->getEnums());
    }

    public function testSetAndGetMarkers(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getMarkers());

        $mockInstance = new Collection();
        $mock = $mockInstance;

        $this->fixture->setMarkers($mock);

        self::assertSame($mockInstance, $this->fixture->getMarkers());
    }

    public function testIfErrorsAreInitializedToAnEmptyCollectionOnInstantiation(): void
    {
        // construct
        self::assertInstanceOf(Collection::class, $this->fixture->getAllErrors());

        // default returns empty array
        self::assertObjectHasProperty('items', $this->fixture->getAllErrors());

        $items = $this->fixture->getAllErrors()->getAll();
        self::assertEmpty($items);
    }

    /** @todo Split this test into its various scenario's instead of one big lump */
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
        $mockClassProperties = new PropertyDescriptor();
        $mockClassProperties->setErrors(new Collection($errorClassProperties));

        // setup class-constant check
        $mockClassConstants = new ConstantDescriptor();
        $mockClassConstants->setErrors(new Collection($errorClassConstants));

        // setup class-method check
        $mockClassMethods = new MethodDescriptor();
        $mockClassMethods->setErrors(new Collection($errorClassMethods));

        // setup class check
        $mockClasses = new ClassDescriptor();
        $mockClasses->setProperties(new Collection([$mockClassProperties]));
        $mockClasses->setConstants(new Collection([$mockClassConstants]));
        $mockClasses->setMethods(new Collection([$mockClassMethods]));
        $mockClasses->setErrors(new Collection($errorClasses));

        $this->fixture->getClasses()->set('my-test-class', $mockClasses);

        // setup interface-constant check
        $mockInterfaceConstants = new ConstantDescriptor();
        $mockInterfaceConstants->setErrors(new Collection($errorInterfacesConstants));

        // setup interface-method check
        $mockInterfaceMethods = new MethodDescriptor();
        $mockInterfaceMethods->setErrors(new Collection($errorInterfacesMethods));

        // setup interface check
        $mockInterfaces = new ClassDescriptor();
        $mockInterfaces->setProperties(new Collection());
        $mockInterfaces->setConstants(new Collection([$mockInterfaceConstants]));
        $mockInterfaces->setMethods(new Collection([$mockInterfaceMethods]));
        $mockInterfaces->setErrors(new Collection($errorInterfaces));

        $this->fixture->getClasses()->set('my-test-interface', $mockInterfaces);

        // setup traits-constant check
        $mockTraitsProperties = new ConstantDescriptor();
        $mockTraitsProperties->setErrors(new Collection($errorTraitsProperties));

        // setup traits-method check
        $mockTraitsMethods = new MethodDescriptor();
        $mockTraitsMethods->setErrors(new Collection($errorTraitsMethods));

        // setup traits check
        $mockTraits = new ClassDescriptor();
        $mockTraits->setConstants(new Collection());
        $mockTraits->setProperties(new Collection([$mockTraitsProperties]));
        $mockTraits->setMethods(new Collection([$mockTraitsMethods]));
        $mockTraits->setErrors(new Collection($errorTraits));

        $this->fixture->getClasses()->set('my-test-traits', $mockTraits);

        // setup functions check
        $mockFunctions = new FunctionDescriptor();

        // create dummy instances of constants/methods
        $mockFunctions->setErrors(new Collection($errorFunctions));

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
