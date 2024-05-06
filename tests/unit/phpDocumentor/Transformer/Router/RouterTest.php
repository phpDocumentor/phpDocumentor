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

namespace phpDocumentor\Transformer\Router;

use League\Uri\Uri;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Router\Router
 * @covers ::__construct
 
 */
final class RouterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::generate
     * @dataProvider provideNodesWithExpectedUrls
     */
    public function testItCanGenerateUrlsForAGivenNode(
        $node,
        string $expected,
    ): void {
        $router = new Router();
        $result = $router->generate($node);

        $this->assertSame($expected, $result);
    }

    /** @covers ::generate */
    public function testItCanGenerateUriWhenGivenAUri(): void
    {
        $router = new Router();

        $this->assertSame('https://my/uri', $router->generate(RouterTest::givenAUri()));
    }

    /** @covers ::generate */
    public function testItReturnsAnEmptyStringWhenUnableToGenerateAUrl(): void
    {
        $router = new Router();
        $result = $router->generate(new stdClass()); // An stdClass is not routable

        $this->assertSame('', $result);
    }

    public static function provideNodesWithExpectedUrls(): array
    {
        return [
            'for a file' => [RouterTest::givenAFileDescriptor(), '/files/my-file.html'],
            'for a package' => [RouterTest::givenAPackageDescriptor(), '/packages/My-Package.html'],
            'for a namespace' => [RouterTest::givenANamespaceDescriptor(), '/namespaces/my-namespace.html'],
            'for a function' => [
                RouterTest::givenAFunctionDescriptor(),
                '/namespaces/my-namespace.html#function_myFunction',
            ],
            'for a global constant' => [
                RouterTest::givenAGlobalConstantDescriptor(),
                '/namespaces/my-namespace.html#constant_MY_CONSTANT',
            ],
            'for a trait' => [RouterTest::givenATraitDescriptor(), '/classes/My-Trait.html'],
            'for an interface' => [RouterTest::givenAnInterfaceDescriptor(), '/classes/My-Interface.html'],
            'for a class' => [RouterTest::givenAClassDescriptor(), '/classes/My-Class.html'],
            'for a method' => [RouterTest::givenAMethodDescriptor(), '/classes/My-Class.html#method_myMethod'],
            'for a property' => [RouterTest::givenAPropertyDescriptor(), '/classes/My-Class.html#property_myProperty'],
            'for a class constant' => [
                RouterTest::givenAClassConstantDescriptor(),
                '/classes/My-Class.html#constant_MY_CONSTANT',
            ],
            'for an fqsen' => [RouterTest::givenAnFqsen(), '/classes/My-Class.html#method_myMethod'],
        ];
    }

    private static function givenAFileDescriptor(): FileDescriptor
    {
        $descriptor = new FileDescriptor('abc');
        $descriptor->setPath('My/File.php');

        return $descriptor;
    }

    private static function givenAPackageDescriptor(): PackageDescriptor
    {
        $descriptor = new PackageDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Package'));

        return $descriptor;
    }

    private static function givenANamespaceDescriptor(): NamespaceDescriptor
    {
        $descriptor = new NamespaceDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Namespace'));

        return $descriptor;
    }

    private static function givenAFunctionDescriptor(): FunctionDescriptor
    {
        $descriptor = new FunctionDescriptor();
        $descriptor->setName('myFunction');
        $descriptor->setNamespace(RouterTest::givenANamespaceDescriptor());

        return $descriptor;
    }

    private static function givenAGlobalConstantDescriptor(): ConstantDescriptor
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setName('MY_CONSTANT');
        $descriptor->setNamespace(RouterTest::givenANamespaceDescriptor());

        return $descriptor;
    }

    private static function givenATraitDescriptor(): TraitDescriptor
    {
        $descriptor = new TraitDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Trait'));

        return $descriptor;
    }

    private static function givenAnInterfaceDescriptor(): InterfaceDescriptor
    {
        $descriptor = new InterfaceDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Interface'));

        return $descriptor;
    }

    private static function givenAClassDescriptor(): ClassDescriptor
    {
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));

        return $descriptor;
    }

    private static function givenAMethodDescriptor(): MethodDescriptor
    {
        $descriptor = new MethodDescriptor();
        $descriptor->setName('myMethod');
        $descriptor->setParent(RouterTest::givenAClassDescriptor());

        return $descriptor;
    }

    private static function givenAPropertyDescriptor(): PropertyDescriptor
    {
        $descriptor = new PropertyDescriptor();
        $descriptor->setName('myProperty');
        $descriptor->setParent(RouterTest::givenAClassDescriptor());

        return $descriptor;
    }

    private static function givenAClassConstantDescriptor(): ConstantDescriptor
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setName('MY_CONSTANT');
        $descriptor->setParent(RouterTest::givenAClassDescriptor());

        return $descriptor;
    }

    private static function givenAnFqsen(): Fqsen
    {
        return new Fqsen('\My\Class::myMethod()');
    }

    private static function givenAUri(): Uri
    {
        return Uri::createFromString('https://my/uri');
    }
}
