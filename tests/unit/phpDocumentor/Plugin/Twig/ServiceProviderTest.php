<?php

declare(strict_types=1);

namespace phpDocumentor\Plugin\Twig;

use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Writer\Collection;
use phpDocumentor\Translator\Translator;
use Pimple\Container;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;

final class ServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    /** @var Container */
    private $container;

    /** @var Collection */
    private $writerCollection;

    public function setUp()
    {
        $this->container = new Container(new SymfonyContainer());
        $this->writerCollection = new Collection(new Queue());

        $this->container['translator'] = new Translator();
        $this->container['transformer.writer.collection'] = $this->writerCollection;
        $this->container[\Twig\Environment::class] = new \Twig\Environment();
    }

    public function test_that_the_twig_writer_is_set()
    {
        $serviceProvider = new ServiceProvider();
        $serviceProvider->register($this->container);

        $this->assertTrue(isset($this->writerCollection['twig']));
    }
}
