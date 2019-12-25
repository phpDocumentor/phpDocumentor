<?php

declare(strict_types=1);

namespace phpDocumentor\Faker;

use Faker\Provider\Base;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Mockery as m;
use phpDocumentor\Configuration\SymfonyConfigFactory;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class Provider extends Base
{
    public function fileSystem() : Filesystem
    {
        return new Filesystem(new NullAdapter());
    }

    public function template(string $name = 'test') : Template
    {
        return new Template($name, new MountManager([
            'template' => $this->fileSystem(),
            'templates' => $this->fileSystem(),
            'destination' => $this->fileSystem(),
        ]));
    }

    public function transformation(?Template $template = null) : Transformation
    {
        return new Transformation($template ?: $this->template(), '', '', '', '');
    }

    public function transformer(?Template\Collection $templateCollection = null) : Transformer
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

    public function configTreeBuilder(string $version) : TreeBuilder
    {
        $treebuilder = new TreeBuilder('test');
        $treebuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode(SymfonyConfigFactory::FIELD_CONFIG_VERSION)->defaultValue($version)->end();

        return $treebuilder;
    }
}
