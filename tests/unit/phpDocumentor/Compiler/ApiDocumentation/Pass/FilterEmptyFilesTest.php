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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/** @link https://github.com/phpDocumentor/phpDocumentor/issues/3997 */
final class FilterEmptyFilesTest extends TestCase
{
    use Faker;

    public function testFilesWithoutDocumentedElementsAreRemoved(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();

        $documentedFile = self::fileWithClass('src/Documented.php', new Fqsen('\My\DocumentedClass'));
        $emptyFile = new FileDescriptor('empty-hash');
        $emptyFile->setPath('src/Empty.php');

        $apiSet->getFiles()->set($documentedFile->getPath(), $documentedFile);
        $apiSet->getFiles()->set($emptyFile->getPath(), $emptyFile);

        (new FilterEmptyFiles())($apiSet);

        self::assertCount(1, $apiSet->getFiles());
        self::assertSame($documentedFile, $apiSet->getFiles()->get($documentedFile->getPath()));
    }

    /** @dataProvider filesWithSingleElementProvider */
    public function testFilesWithAnyKindOfDocumentedElementAreKept(callable $populate): void
    {
        $apiSet = self::faker()->apiSetDescriptor();

        $file = new FileDescriptor('with-element-hash');
        $file->setPath('src/WithElement.php');
        $populate($file);

        $apiSet->getFiles()->set($file->getPath(), $file);

        (new FilterEmptyFiles())($apiSet);

        self::assertCount(1, $apiSet->getFiles());
        self::assertSame($file, $apiSet->getFiles()->get($file->getPath()));
    }

    /** @return iterable<string, array{callable(FileDescriptor): void}> */
    public static function filesWithSingleElementProvider(): iterable
    {
        yield 'class' => [
            static function (FileDescriptor $file): void {
                $class = self::faker()->classDescriptor(new Fqsen('\\My\\SomeClass'));
                $file->getClasses()->set((string) $class->getFullyQualifiedStructuralElementName(), $class);
            },
        ];

        yield 'interface' => [
            static function (FileDescriptor $file): void {
                $interface = self::faker()->interfaceDescriptor(new Fqsen('\\My\\SomeInterface'));
                $file->getInterfaces()->set(
                    (string) $interface->getFullyQualifiedStructuralElementName(),
                    $interface,
                );
            },
        ];

        yield 'trait' => [
            static function (FileDescriptor $file): void {
                $trait = self::faker()->traitDescriptor(new Fqsen('\\My\\SomeTrait'));
                $file->getTraits()->set((string) $trait->getFullyQualifiedStructuralElementName(), $trait);
            },
        ];

        yield 'enum' => [
            static function (FileDescriptor $file): void {
                $enum = self::faker()->enumDescriptor(new Fqsen('\\My\\SomeEnum'));
                $file->getEnums()->set((string) $enum->getFullyQualifiedStructuralElementName(), $enum);
            },
        ];

        yield 'constant' => [
            static function (FileDescriptor $file): void {
                $constant = self::faker()->constantDescriptor(new Fqsen('\\MY_CONST'));
                $file->getConstants()->set((string) $constant->getFullyQualifiedStructuralElementName(), $constant);
            },
        ];

        yield 'function' => [
            static function (FileDescriptor $file): void {
                $function = new FunctionDescriptor();
                $function->setName('someFunction');
                $function->setFullyQualifiedStructuralElementName(new Fqsen('\\someFunction()'));
                $file->getFunctions()->set((string) $function->getFullyQualifiedStructuralElementName(), $function);
            },
        ];
    }

    private static function fileWithClass(string $path, Fqsen $classFqsen): FileDescriptor
    {
        $file = new FileDescriptor('class-hash-' . $path);
        $file->setPath($path);
        $file->getClasses()->set((string) $classFqsen, self::faker()->classDescriptor($classFqsen));

        return $file;
    }
}
