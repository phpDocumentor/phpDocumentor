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
use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\FileSystem\FlySystemFactory;
use phpDocumentor\Dsn;
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
use const DIRECTORY_SEPARATOR;

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
            'guides' => $this->fileSystem(),
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

    public function source() : Source
    {
        return new Source(
            $this->dsn(),
            [$this->path()]
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
        return new Path(implode(DIRECTORY_SEPARATOR, $this->generator->words(5)));
    }

    public function versionSpecification() : VersionSpecification
    {
        return new VersionSpecification(
            $this->generator->numerify('v##.##'),
            (string) $this->path(),
            [ApiSpecification::createFromArray(
                [
                    'source' => [],
                    'output' => 'a',
                ]
            ),
            ],
            []
        );
    }

    /** @param FileDescriptor[] $files */
    public function apiSetDescriptor(array $files = []): ApiSetDescriptor
    {
        $set = new ApiSetDescriptor(
            $this->generator->word(),
            $this->source(),
            (string) $this->path(),
            $this->apiSpecification()
        );

        foreach ($files as $file) {
            $set->addFile($file);
        }

        return $set;
    }

    public function apiSetDescriptorWithFiles(int $numberOfFiles = 2): ApiSetDescriptor
    {
        $files = [];
        for ($i = 0; $i < $numberOfFiles; $i++) {
            $files[] = $this->fileDescriptor();
        }

        return $this->apiSetDescriptor($files);
    }

    public function fileDescriptor() : FileDescriptor
    {
        $file = new FileDescriptor($this->generator->md5);
        $file->setPath((string) $this->path());

        return $file;
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

    public function classDescriptor(?string $className = null, string $namespace = '\\'): ClassDescriptor
    {
        $parts = $this->generator->words();
        $className = $className ?? '\\' . implode('\\', $parts);
        array_pop($parts);
        $namespace = $namespace ?? '\\' . implode('\\', $parts);

        $classDescriptor = new ClassDescriptor();
        $classDescriptor->setNamespace($namespace);
        $classDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $classDescriptor;
    }

    public function interfaceDescriptor(?string $className = null, string $namespace = '\\'): InterfaceDescriptor
    {
        $parts = $this->generator->words();
        $className = $className ?? '\\' . implode('\\', $parts);
        array_pop($parts);
        $namespace = $namespace ?? '\\' . implode('\\', $parts);

        $interfaceDescriptor = new InterfaceDescriptor();
        $interfaceDescriptor->setNamespace($namespace);
        $interfaceDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $interfaceDescriptor;
    }

    public function traitDescriptor(string $className, string $namespace = '\\'): TraitDescriptor
    {
        $traitDescriptor = new TraitDescriptor();
        $traitDescriptor->setNamespace($namespace);
        $traitDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $traitDescriptor;
    }

    public function functionDescriptor(string $className, string $namespace = '\\'): FunctionDescriptor
    {
        $functionDescriptor = new FunctionDescriptor();
        $functionDescriptor->setNamespace($namespace);
        $functionDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $functionDescriptor;
    }

    public function constantDescriptor(string $className, string $namespace = '\\'): ConstantDescriptor
    {
        $constantDescriptor = new ConstantDescriptor();
        $constantDescriptor->setNamespace($namespace);
        $constantDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $constantDescriptor;
    }

    public function propertyDescriptor(string $className): PropertyDescriptor
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $propertyDescriptor;
    }

    public function methodDescriptor(string $className): MethodDescriptor
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($className));

        return $methodDescriptor;
    }
}
