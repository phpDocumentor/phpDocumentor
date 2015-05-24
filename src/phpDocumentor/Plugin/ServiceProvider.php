<?php

namespace phpDocumentor\Plugin;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Configuration as ApplicationConfiguration;

class ServiceProvider implements ServiceProviderInterface
{
    /** @var \DI\Container */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function register(Application $app)
    {
        /** @var ApplicationConfiguration $config */
        $config = $this->container->get(ApplicationConfiguration::class);
        $plugins = $config->getPlugins();

        if (! $plugins) {
            $app->register(new Core\ServiceProvider(null, $this->container));
            $app->register(new Scrybe\ServiceProvider(null, $this->container));

            return;
        }

        array_walk(
            $plugins,
            function ($plugin) use ($app) {
                /** @var Plugin $plugin */
                $provider = (strpos($plugin->getClassName(), '\\') === false)
                    ? sprintf('phpDocumentor\\Plugin\\%s\\ServiceProvider', $plugin->getClassName())
                    : $plugin->getClassName();
                if (!class_exists($provider)) {
                    throw new \RuntimeException('Loading Service Provider for ' . $provider . ' failed.');
                }

                try {
                    $app->register(new $provider($plugin, $this->container));
                } catch (\InvalidArgumentException $e) {
                    throw new \RuntimeException($e->getMessage());
                }
            }
        );
    }
}
