<?php

declare(strict_types=1);

namespace phpDocumentor\Faker;

use Faker\Provider\Base;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Mockery as m;
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Source;
use phpDocumentor\Configuration\SymfonyConfigFactory;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Path;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

use function array_pop;
use function implode;

final class Provider extends Base
{
    public function fileSystem(): Filesystem
    {
        return new Filesystem(new NullAdapter());
    }

    public function template(string $name = 'test'): Template
    {
        return new Template($name, new MountManager([
            'template' => $this->fileSystem(),
            'templates' => $this->fileSystem(),
            'destination' => $this->fileSystem(),
        ]));
    }

    public function transformation(?Template $template = null): Transformation
    {
        return new Transformation($template ?? $this->template(), '', '', '', '');
    }

    public function transformer(?Template\Collection $templateCollection = null): Transformer
    {
        if ($templateCollection === null) {
            $templateCollection = m::mock(Template\Collection::class);
            $templateCollection->shouldIgnoreMissing();
        }

        $writerCollectionMock = m::mock(Collection::class);
        $writerCollectionMock->shouldIgnoreMissing();

        return new Transformer(
            $templateCollection,
            $writerCollectionMock,
            new NullLogger(),
            $this->flySystemFactory()
        );
    }

    /**
     * @return m\LegacyMockInterface|m\MockInterface|FlySystemFactory
     */
    public function flySystemFactory()
    {
        return new FlySystemFactory(new MountManager());
    }

    public function configTreeBuilder(string $version): TreeBuilder
    {
        $treebuilder = new TreeBuilder('test');
        $treebuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode(SymfonyConfigFactory::FIELD_CONFIG_VERSION)->defaultValue($version)->end();

        return $treebuilder;
    }

    public function phpParserContext(): ContextStack
    {
        return new ContextStack(
            new Project('test')
        );
    }

    public function apiSpecification(): ApiSpecification
    {
        return ApiSpecification::createDefault();
    }

    public function dsn(): Dsn
    {
        return Dsn::createFromString('file:///source');
    }

    public function path(): Path
    {
        return new Path('./');
    }

    public function source(): Source
    {
        return new Source(
            $this->dsn(),
            [$this->path()]
        );
    }

    public function fileDescriptor(): FileDescriptor
    {
        $file = new FileDescriptor($this->generator->md5);
        $file->setPath((string) $this->path());
        $file->setSource($this->generator->words(10, true));

        return $file;
    }

    /** @param DocumentationSetDescriptor[] $documentationSets */
    public function versionDescriptor(array $documentationSets): VersionDescriptor
    {
        return new VersionDescriptor(
            $this->generator->numerify('v#.#.#'),
            DescriptorCollection::fromClassString(DocumentationSetDescriptor::class, $documentationSets)
        );
    }

    public function apiSetDescriptor(): ApiSetDescriptor
    {
        return new ApiSetDescriptor(
            $this->generator->word(),
            $this->source(),
            (string) $this->path(),
            $this->apiSpecification()
        );
    }

    public function guideSetDescriptor(): GuideSetDescriptor
    {
        return new GuideSetDescriptor(
            $this->generator->word(),
            $this->source(),
            (string) $this->path(),
            'rst'
        );
    }

    public function projectDescriptor(): ProjectDescriptor
    {
        return new ProjectDescriptor('test');
    }

    public function namespaceDescriptor(Fqsen $fqsen, array $children = []): NamespaceDescriptor
    {
        $namespace = new NamespaceDescriptor();
        $namespace->setName($fqsen->getName());
        $namespace->setFullyQualifiedStructuralElementName($fqsen);

        foreach ($children as $child) {
            $namespace->addChild($child);
        }

        return $namespace;
    }

    public function namespaceDescriptorTree($maxDepth = 3, $amount = 3): NamespaceDescriptor
    {
        $maxDepth--;
        $rootNamespace = $this->namespaceDescriptor(new Fqsen('\\' . $this->generator->word));

        for ($namespaces = 0; $namespaces < $amount; $namespaces++) {
            $parts         = $this->generator->words($maxDepth);
            $namespace = null;
            for ($i = $maxDepth; $i > 0; $i--) {
                $fqsen = new Fqsen('\\' . $rootNamespace->getName() . '\\' . implode('\\', $parts));
                array_pop($parts);

                $namespace = $this->namespaceDescriptor($fqsen, $namespace ? [$namespace] : []);
            }

            $rootNamespace->addChild($namespace);
        }

        return $rootNamespace;
    }

    public function fqsen($maxDepth = 3): Fqsen
    {
        $parts = $this->generator->words($maxDepth);

        return new Fqsen('\\' . implode('\\', $parts));
    }

    public function classDescriptor(): ClassDescriptor
    {
        $classDescriptor = new ClassDescriptor();
        $classDescriptor->setFullyQualifiedStructuralElementName($this->fqsen());

        return $classDescriptor;
    }
}
