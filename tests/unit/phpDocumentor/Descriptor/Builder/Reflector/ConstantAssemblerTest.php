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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Expression;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Test class for \phpDocumentor\Descriptor\Reflector\ConstantAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler
 */
final class ConstantAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ConstantAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->fixture = new ConstantAssembler();
        $projectDescriptorBuilder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture->setBuilder($projectDescriptorBuilder->reveal());
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateGlobalConstantDescriptorFromReflector(): void
    {
        $value = '\\false';
        $name = 'constBoolean';

        $docBlockDescription = new DocBlock\Description(
            <<<'DOCBLOCK'
            /**
             * This is a example description
             */
DOCBLOCK,
        );

        $docBlock = new DocBlock('This is a example description', $docBlockDescription);
        $constantReflector = new Constant(new Fqsen('\\' . $name), $docBlock, new Expression('\false'));

        $descriptor = $this->fixture->create($constantReflector);

        self::assertSame($name, $descriptor->getName());
        self::assertSame('\\' . $name, (string) $descriptor->getFullyQualifiedStructuralElementName());
        self::assertSame('', $descriptor->getNamespace());
        self::assertEquals(new Expression('\false'), $descriptor->getValue());
        self::assertEquals(new Visibility(VisibilityModifier::PUBLIC), $descriptor->getVisibility());
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateNamespacedConstantDescriptorFromReflector(): void
    {
        $pi = '3.14159265359';
        $name = 'constPI';
        $namespace = 'Namespace';

        $docBlockDescription = new DocBlock\Description(
            <<<'DOCBLOCK'
            /**
             * This is a example description
             */
DOCBLOCK,
        );

        $docBlockMock = new DocBlock('This is a example description', $docBlockDescription);
        $constantReflectorMock = new Constant(new Fqsen('\\' . $namespace . '::' . $name), $docBlockMock, $pi);

        $descriptor = $this->fixture->create($constantReflectorMock);

        self::assertSame($name, $descriptor->getName());
        self::assertSame(
            '\\' . $namespace . '::' . $name,
            (string) $descriptor->getFullyQualifiedStructuralElementName(),
        );
        self::assertSame('\\' . $namespace, $descriptor->getNamespace());
        self::assertSame($pi, (string) $descriptor->getValue());
        self::assertEquals(new Visibility(VisibilityModifier::PUBLIC), $descriptor->getVisibility());
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateNamespaceConstantDescriptorFromReflector(): void
    {
        $pi = '3.14159265359';
        $name = 'constPI';
        $namespace = 'Namespace';

        $docBlockDescription = new DocBlock\Description(
            <<<'DOCBLOCK'
            /**
             * This is a example description
             */
DOCBLOCK,
        );

        $docBlockMock = new DocBlock('This is a example description', $docBlockDescription);
        $constantReflectorMock = new Constant(new Fqsen('\\' . $namespace . '\\' . $name), $docBlockMock, $pi);

        $descriptor = $this->fixture->create($constantReflectorMock);

        self::assertSame($name, $descriptor->getName());
        self::assertSame(
            '\\' . $namespace . '\\' . $name,
            (string) $descriptor->getFullyQualifiedStructuralElementName(),
        );
        self::assertSame('\\' . $namespace, $descriptor->getNamespace());
        self::assertSame($pi, (string) $descriptor->getValue());
        self::assertEquals(new Visibility(VisibilityModifier::PUBLIC), $descriptor->getVisibility());
    }
}
