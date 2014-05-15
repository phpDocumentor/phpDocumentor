<?php

namespace phpDocumentor\Configuration;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Serializer;
use phpDocumentor\Console\Input\ArgvInput;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;
use Zend\Config\Factory;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the Configuration object to the DIC.
     *
     * phpDocumentor first loads the template config file (/data/phpdoc.tpl.xml)
     * and then the phpdoc.dist.xml, or the phpdoc.xml if it exists but not both,
     * from the current working directory.
     *
     * The user config file (either phpdoc.dist.xml or phpdoc.xml) is merged
     * with the template file.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        if (!isset($app['serializer.annotations'])) {
            throw new \RuntimeException(
                'The configuration service provider depends on the JmsSerializer Service Provider but the '
                . '"serializer.annotations" key could not be found in the container.'
            );
        }

        $app->extend('console',
            function (ConsoleApplication $console){
                $console->getDefinition()->addOption(
                    new InputOption(
                        'config',
                        'c',
                        InputOption::VALUE_OPTIONAL,
                        'Location of a custom configuration file'
                    )
                );

                return $console;
            }
        );

        // Add annotations to Jms Serializer
        $annotations = $app['serializer.annotations'];
        $annotations[] = array(
            'namespace' => 'phpDocumentor\Configuration\Merger\Annotation',
            'path' => __DIR__ . '/../../'
        );
        $app['serializer.annotations'] = $annotations;

        $app['config.merger'] = $app->share(
            function () {
                return new Merger(new AnnotationReader());
            }
        );

        $app['config.path.template'] = __DIR__ . '/Resources/phpdoc.tpl.xml';
        $app['config.path.user'] = getcwd()
            . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml');
        $app['config.class'] = 'phpDocumentor\Configuration';

        $app['config2'] = $app->share(
            function ($app) {
                /** @var ConsoleApplication $console */
                $console = $app['console'];
                $input = new ArgvInput(null, $console->getDefinition());
                $userConfigFilePath = $input->getOption('config');
                if ($userConfigFilePath && $userConfigFilePath != 'none' && is_readable($userConfigFilePath)) {
                    chdir(dirname($userConfigFilePath));
                } else {
                    $userConfigFilePath = $userConfigFilePath != 'none' ? null : 'none';
                }

                /** @var Serializer $serializer */
                $serializer = $app['serializer'];

                $config = $serializer->deserialize(
                    file_get_contents($app['config.path.template']),
                    $app['config.class'],
                    'xml'
                );

                if ($userConfigFilePath != 'none') {
                    $userConfigFilePath = $userConfigFilePath ?: $app['config.path.user'];
                    $userConfigFile = $serializer->deserialize(
                        file_get_contents($userConfigFilePath),
                        $app['config.class'],
                        'xml'
                    );

                    /** @var Merger $merger */
                    $merger = $app['config.merger'];
                    $config = $merger->run($config, $userConfigFile);
                }

                return $config;
            }
        );

        $app['config'] = $app->share(
            function ($app) {
                $config_files = array($app['config.path.template']);
                if (is_readable($app['config.path.user'])) {
                    $config_files[] = $app['config.path.user'];
                }

                return Factory::fromFiles($config_files, true);
            }
        );
    }
} 