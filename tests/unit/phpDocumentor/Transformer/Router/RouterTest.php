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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Router\Router
 * @covers ::__construct
 * @covers ::<private>
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
        string $routeName,
        string $expected,
        string $fragment,
    ): void {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $url = $routeName . '-' . $expected . '-' . $fragment;
        $urlGenerator->generate($routeName, ['name' => $expected, '_fragment' => $fragment])
            ->willReturn($url);

        $router = new Router(
            new ClassBasedFqsenUrlGenerator($urlGenerator->reveal(), new AsciiSlugger()),
            $urlGenerator->reveal(),
            new AsciiSlugger(),
        );
        $result = $router->generate($node);

        $this->assertSame($url, $result);
    }

    /** @covers ::generate */
    public function testItCanGenerateUriWhenGivenAUri(): void
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate()->shouldNotBeCalled();

        $router = new Router(
            new ClassBasedFqsenUrlGenerator($urlGenerator->reveal(), new AsciiSlugger()),
            $urlGenerator->reveal(),
            new AsciiSlugger(),
        );

        $this->assertSame('https://my/uri', $router->generate($this->givenAUri()));
    }

    /** @covers ::generate */
    public function testItReturnsAnEmptyStringWhenUnableToGenerateAUrl(): void
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate()->shouldNotBeCalled();

        $router = new Router(
            new ClassBasedFqsenUrlGenerator($urlGenerator->reveal(), new AsciiSlugger()),
            $urlGenerator->reveal(),
            new AsciiSlugger(),
        );
        $result = $router->generate(new stdClass()); // An stdClass is not routable

        $this->assertSame('', $result);
    }

    public function provideNodesWithExpectedUrls(): array
    {
        return [
            'for a file' => [$this->givenAFileDescriptor(), 'file', 'my-file', ''],
            'for a package' => [$this->givenAPackageDescriptor(), 'package', 'My-Package', ''],
            'for a namespace' => [$this->givenANamespaceDescriptor(), 'namespace', 'my-namespace', ''],
            'for a function' => [$this->givenAFunctionDescriptor(), 'namespace', 'my-namespace', 'function_myFunction'],
            'for a global constant' => [
                $this->givenAGlobalConstantDescriptor(),
                'namespace',
                'my-namespace',
                'constant_MY_CONSTANT',
            ],
            'for a trait' => [$this->givenATraitDescriptor(), 'class', 'My-Trait', ''],
            'for an interface' => [$this->givenAnInterfaceDescriptor(), 'class', 'My-Interface', ''],
            'for a class' => [$this->givenAClassDescriptor(), 'class', 'My-Class', ''],
            'for a method' => [$this->givenAMethodDescriptor(), 'class', 'My-Class', 'method_myMethod'],
            'for a property' => [$this->givenAPropertyDescriptor(), 'class', 'My-Class', 'property_myProperty'],
            'for a class constant' => [
                $this->givenAClassConstantDescriptor(),
                'class',
                'My-Class',
                'constant_MY_CONSTANT',
            ],
            'for an fqsen' => [$this->givenAnFqsen(), 'class', 'My-Class', 'method_myMethod'],
        ];
    }

    private function givenAFileDescriptor(): FileDescriptor
    {
        $descriptor = new FileDescriptor('abc');
        $descriptor->setPath('My/File.php');

        return $descriptor;
    }

    private function givenAPackageDescriptor(): PackageDescriptor
    {
        $descriptor = new PackageDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Package'));

        return $descriptor;
    }

    private function givenANamespaceDescriptor(): NamespaceDescriptor
    {
        $descriptor = new NamespaceDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Namespace'));

        return $descriptor;
    }

    private function givenAFunctionDescriptor(): FunctionDescriptor
    {
        $descriptor = new FunctionDescriptor();
        $descriptor->setName('myFunction');
        $descriptor->setNamespace($this->givenANamespaceDescriptor());

        return $descriptor;
    }

    private function givenAGlobalConstantDescriptor(): ConstantDescriptor
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setName('MY_CONSTANT');
        $descriptor->setNamespace($this->givenANamespaceDescriptor());

        return $descriptor;
    }

    private function givenATraitDescriptor(): TraitDescriptor
    {
        $descriptor = new TraitDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Trait'));

        return $descriptor;
    }

    private function givenAnInterfaceDescriptor(): InterfaceDescriptor
    {
        $descriptor = new InterfaceDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Interface'));

        return $descriptor;
    }

    private function givenAClassDescriptor(): ClassDescriptor
    {
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));

        return $descriptor;
    }

    private function givenAMethodDescriptor(): MethodDescriptor
    {
        $descriptor = new MethodDescriptor();
        $descriptor->setName('myMethod');
        $descriptor->setParent($this->givenAClassDescriptor());

        return $descriptor;
    }

    private function givenAPropertyDescriptor(): PropertyDescriptor
    {
        $descriptor = new PropertyDescriptor();
        $descriptor->setName('myProperty');
        $descriptor->setParent($this->givenAClassDescriptor());

        return $descriptor;
    }

    private function givenAClassConstantDescriptor(): ConstantDescriptor
    {
        $descriptor = new ConstantDescriptor();
        $descriptor->setName('MY_CONSTANT');
        $descriptor->setParent($this->givenAClassDescriptor());

        return $descriptor;
    }

    private function givenAnFqsen(): Fqsen
    {
        return new Fqsen('\My\Class::myMethod()');
    }

    private function givenAUri(): Uri
    {
        return Uri::createFromString('https://my/uri');
    }
}
