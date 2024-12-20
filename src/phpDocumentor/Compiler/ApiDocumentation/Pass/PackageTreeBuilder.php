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

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Pipeline\Attribute\Stage;
use phpDocumentor\Reflection\Fqsen;
use Webmozart\Assert\Assert;

use function explode;
use function ltrim;
use function preg_replace;
use function rtrim;
use function str_replace;
use function ucfirst;

/**
 * Rebuilds the package tree from the elements found in files.
 *
 * On every compiler pass is the package tree rebuild to aid in the process
 * of incremental updates.
 *
 * If the package tree were to be persisted then both locations needed to be
 * invalidated if a file were to change.
 */
#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9001,
    'Build "packages" index',
)]
final class PackageTreeBuilder extends ApiDocumentationPass
{
    public function __construct(private readonly Parser $parser)
    {
    }

    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        if ($subject->getSettings()->ignorePackages()) {
            return $subject;
        }

        $package = $subject->getPackage();
        Assert::isInstanceOf($package, PackageInterface::class);

        $packages = Collection::fromInterfaceString(PackageInterface::class);
        $packages['\\'] = $package;

        /** @var FileInterface $file */
        foreach ($subject->getFiles() as $file) {
            $this->addElementsOfTypeToPackage($packages, [$file], 'files');
            $this->addElementsOfTypeToPackage($packages, $file->getConstants()->getAll(), 'constants');
            $this->addElementsOfTypeToPackage($packages, $file->getFunctions()->getAll(), 'functions');
            $this->addElementsOfTypeToPackage($packages, $file->getClasses()->getAll(), 'classes');
            $this->addElementsOfTypeToPackage($packages, $file->getInterfaces()->getAll(), 'interfaces');
            $this->addElementsOfTypeToPackage($packages, $file->getTraits()->getAll(), 'traits');
            $this->addElementsOfTypeToPackage($packages, $file->getEnums()->getAll(), 'enums');
        }

        $subject->getIndexes()->set(
            'packages',
            Collection::fromInterfaceString(ElementInterface::class, $packages->getAll()),
        );

        return $subject;
    }

    /**
     * Adds the given elements of a specific type to their respective Package Descriptors.
     *
     * This method will assign the given elements to the package as registered in the package field of that
     * element. If a package does not exist yet it will automatically be created.
     *
     * @param Collection<PackageInterface> $packages
     * @param array<ElementInterface> $elements Series of elements to add to their respective package.
     * @param string $type     Declares which field of the package will be populated with the given
     *                         series of elements. This name will be transformed to a getter which must exist. Out of
     *                         performance considerations will no effort be done to verify whether the provided type is
     *                         valid.
     */
    private function addElementsOfTypeToPackage(Collection $packages, array $elements, string $type): void
    {
        foreach ($elements as $element) {
            $packageName = '';
            $packageTags = $element->getTags()->fetch('package');
            if ($packageTags instanceof Collection) {
                $packageTag = $packageTags->getIterator()->current();
                if ($packageTag instanceof TagDescriptor) {
                    $packageName = $this->normalizePackageName((string) $packageTag->getDescription());
                }
            }

            $subpackageCollection = $element->getTags()->fetch('subpackage');
            if ($subpackageCollection instanceof Collection && $subpackageCollection->count() > 0) {
                $subpackageTag = $subpackageCollection->getIterator()->current();
                if ($subpackageTag instanceof TagDescriptor) {
                    $packageName .= '\\' . $this->normalizePackageName((string) $subpackageTag->getDescription());
                }
            }

            if ($packageName === '') {
                $packageName = $this->parser->getDefaultPackageName();
            }

            // ensure consistency by trimming the slash prefix and then re-appending it.
            $packageIndexName = '\\' . ltrim($packageName, '\\');
            if (! isset($packages[$packageIndexName])) {
                $this->createPackageDescriptorTree($packages, $packageName);
            }

            /** @var PackageInterface $package */
            $package = $packages[$packageIndexName];

            // replace textual representation with an object representation
            $element->setPackage($package);

            // add element to package
            $getter = 'get' . ucfirst($type);

            /** @var Collection<ElementInterface> $collection */
            $collection = $package->{$getter}();
            $collection->add($element);
        }
    }

    /**
     * Creates a tree of PackageDescriptors based on the provided FQNN (package name).
     *
     * This method will examine the package name and create a package descriptor for each part of
     * the FQNN if it doesn't exist in the packages field of the current package (starting with the root
     * Package in the Project Descriptor),
     *
     * As an intended side effect this method also populates the *elements* index of the ProjectDescriptor with all
     * created PackageDescriptors. Each index key is prefixed with a tilde (~) so that it will not conflict with
     * other FQSEN's, such as classes or interfaces.
     *
     * @see PackageInterface::getChildren() for the child packages of a given package.
     * @see DocumentationSetDescriptor::getPackage() for the root package.
     *
     * @param Collection<PackageInterface> $packages
     * @param string $packageName A FQNN of the package (and parents) to create.
     */
    private function createPackageDescriptorTree(Collection $packages, string $packageName): void
    {
        $parts = explode('\\', ltrim($packageName, '\\'));
        $fqnn = '';

        // this method does not use recursion to traverse the tree but uses a pointer that will be overridden with the
        // next item that is to be traversed (child package) at the end of the loop.

        /** @var PackageInterface $pointer */
        $pointer = $packages['\\'];
        foreach ($parts as $part) {
            $fqnn .= '\\' . $part;
            if ($pointer->getChildren()->fetch($part)) {
                $pointer = $pointer->getChildren()->get($part);
                continue;
            }

            // package does not exist, create it
            $interimPackageDescriptor = new PackageDescriptor();
            $interimPackageDescriptor->setParent($pointer);
            $interimPackageDescriptor->setName($part);
            $interimPackageDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($fqnn));

            // add to the pointer's list of children
            $pointer->getChildren()->set($part ?: 'UNKNOWN', $interimPackageDescriptor);

            // add to index
            $packages[$fqnn] = $interimPackageDescriptor;

            // move pointer forward
            $pointer = $interimPackageDescriptor;
        }
    }

    /**
     * Converts all old-style separators into namespace-style separators.
     *
     * Please note that the trim will, by design, remove any trailing spearators. This makes it easier to
     * integrate in the rest of this class and allows `\My[Package]` to convert to `\My\Package`.
     */
    private function normalizePackageName(string $packageName): string
    {
        $name = rtrim(str_replace(['.', '_', '-', '[', ']'], ['\\', '\\', '\\', '\\', '\\'], $packageName), '\\');

        return preg_replace('/[^A-Za-z0-9\\\\]/', '', $name);
    }
}
