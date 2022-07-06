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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\DocBlock\Description;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\PackageTreeBuilder
 * @covers ::<private>
 * @covers ::<protected>
 */
final class PackageTreeBuilderTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    private const DEFAULT_PACKAGE_NAME = 'Default';

    /** @var PackageTreeBuilder $fixture */
    private $fixture;

    protected function setUp(): void
    {
        $parser = $this->prophesize(Parser::class);
        $parser->getDefaultPackageName()->willReturn(self::DEFAULT_PACKAGE_NAME);

        $this->fixture = new PackageTreeBuilder($parser->reveal());
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription(): void
    {
        $this->assertSame(
            'Build "packages" index',
            $this->fixture->getDescription()
        );
    }

    /**
     * @covers ::execute
     */
    public function testRootPackageIsSet(): void
    {
        $project = $this->faker()->apiSetDescriptor();
        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages['\\']));
    }

    /**
     * @covers ::execute
     */
    public function testFilesAreIncludedInTheIndex(): void
    {
        $packageName = '\\My\\Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages[$packageName]));
        $this->assertContains($file, $packages[$packageName]->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testPackagesAreSetOnTheDescriptors(): void
    {
        $packageName = '\\My\\Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $this->assertNull($file->getPackage());

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages[$packageName]));
        $this->assertContains($file, $packages[$packageName]->getFiles()->getAll());
        $this->assertSame($packages[$packageName], $file->getPackage());
    }

    /**
     * @covers ::execute
     */
    public function testMultipleElementsInTheSamePackageAreProperlyNestedUnderTheSamePackageDescriptor(): void
    {
        $packageName = '\\My\\Package';

        $file1 = new FileDescriptor('hash');
        $this->withPackage($packageName, $file1);
        $file2 = new FileDescriptor('hash');
        $this->withPackage($packageName, $file2);

        $project = $this->givenProjectWithFiles([$file1, $file2]);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages[$packageName]));
        $this->assertContains($file1, $packages[$packageName]->getFiles()->getAll());
        $this->assertContains($file2, $packages[$packageName]->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testNestedPackagesWillBeCorrectlyFormedIntoATree(): void
    {
        $packageName = '\\My\\Package';
        $subPackageName = '\\My\\Package\\ButDeeper';

        $file1 = new FileDescriptor('hash');
        $this->withPackage($packageName, $file1);
        $file2 = new FileDescriptor('hash');
        $this->withPackage($subPackageName, $file2);

        $project = $this->givenProjectWithFiles([$file1, $file2]);

        $this->fixture->execute($project);

        $rootPackage = $project->getIndexes()->get('packages')['\\'];
        $this->assertNotNull($rootPackage->getChildren()['My']);
        $this->assertNotNull($rootPackage->getChildren()['My']->getChildren()['Package']);
        $this->assertNotNull($rootPackage->getChildren()['My']->getChildren()['Package']->getChildren()['ButDeeper']);
    }

    /**
     * @covers ::execute
     */
    public function testPackagesMayHaveUnderscoresAsSeparators(): void
    {
        $packageName = 'My_Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages['\\My\\Package']));
        $this->assertContains($file, $packages['\\My\\Package']->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testPackagesMayHaveHyphensAsSeparators(): void
    {
        $packageName = 'My-Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages['\\My\\Package']));
        $this->assertContains($file, $packages['\\My\\Package']->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testPackagesMayHaveSquareBracketsAsSeparators(): void
    {
        $packageName = 'My[Package]';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages['\\My\\Package']));
        $this->assertContains($file, $packages['\\My\\Package']->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testPackagesMayHaveDotsAsSeparators(): void
    {
        $packageName = 'My.Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages['\\My\\Package']));
        $this->assertContains($file, $packages['\\My\\Package']->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testSubpackagesAndPackagesAreMergedIntoOne(): void
    {
        $packageName = '\\My';
        $subPackageName = 'Sub\\Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $this->withSubpackage($subPackageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages[$packageName . '\\' . $subPackageName]));
        $this->assertContains($file, $packages[$packageName . '\\' . $subPackageName]->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testSubpackagesMayHaveSlashesAsPrefix(): void
    {
        $packageName = '\\My';
        $subPackageName = '\\Sub\\Package';

        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $this->withSubpackage($subPackageName, $file);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        $this->assertTrue(isset($packages[$packageName . '\\' . $subPackageName]));
        $this->assertContains($file, $packages[$packageName . '\\' . $subPackageName]->getFiles()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testConstantsInAFileAreIncludedInTheIndex(): void
    {
        $constant = new ConstantDescriptor();

        $packageName = '\\My\\Package';
        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $file->getConstants()->add($constant);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        // @todo: shouldn't this constant have inherited his file's Package?
        $this->assertContains($constant, $packages['\\' . self::DEFAULT_PACKAGE_NAME]->getConstants()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testFunctionsInAFileAreIncludedInTheIndex(): void
    {
        $function = new FunctionDescriptor();

        $packageName = '\\My\\Package';
        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $file->getFunctions()->add($function);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        // @todo: shouldn't this function have inherited his file's Package?
        $this->assertContains($function, $packages['\\' . self::DEFAULT_PACKAGE_NAME]->getFunctions()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testInterfacesInAFileAreIncludedInTheIndex(): void
    {
        $interface = new InterfaceDescriptor();

        $packageName = '\\My\\Package';
        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $file->getInterfaces()->add($interface);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        // @todo: shouldn't this interface have inherited his file's Package?
        $this->assertContains($interface, $packages['\\' . self::DEFAULT_PACKAGE_NAME]->getInterfaces()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testTraitsInAFileAreIncludedInTheIndex(): void
    {
        $trait = new TraitDescriptor();

        $packageName = '\\My\\Package';
        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $file->getTraits()->add($trait);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        // @todo: shouldn't this trait have inherited his file's Package?
        $this->assertContains($trait, $packages['\\' . self::DEFAULT_PACKAGE_NAME]->getTraits()->getAll());
    }

    /**
     * @covers ::execute
     */
    public function testClassesInAFileAreIncludedInTheIndex(): void
    {
        $class = new ClassDescriptor();

        $packageName = '\\My\\Package';
        $file = new FileDescriptor('hash');
        $this->withPackage($packageName, $file);
        $file->getClasses()->add($class);

        $project = $this->givenProjectWithFile($file);

        $this->fixture->execute($project);

        $packages = $project->getIndexes()->get('packages');

        // @todo: shouldn't this class have inherited his file's Package?
        $this->assertContains($class, $packages['\\' . self::DEFAULT_PACKAGE_NAME]->getClasses()->getAll());
    }

    private function withPackage(string $packageName, DescriptorAbstract $file): void
    {
        $packageTag = new TagDescriptor('package');
        $packageTag->setDescription(new DescriptionDescriptor(new Description($packageName), []));

        $file->getTags()['package'] = new Collection([$packageTag]);
    }

    private function withSubpackage(string $packageName, DescriptorAbstract $file): void
    {
        $packageTag = new TagDescriptor('subpackage');
        $packageTag->setDescription(new DescriptionDescriptor(new Description($packageName), []));

        $file->getTags()['subpackage'] = new Collection([$packageTag]);
    }

    private function givenProjectWithFile(FileDescriptor $file): ApiSetDescriptor
    {
        $project = $this->faker()->apiSetDescriptor();
        $project->getFiles()->add($file);

        return $project;
    }

    private function givenProjectWithFiles(array $files): ApiSetDescriptor
    {
        $project = $this->faker()->apiSetDescriptor();
        foreach ($files as $file) {
            $project->getFiles()->add($file);
        }

        return $project;
    }
}
