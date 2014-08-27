<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Serializer;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputOption;

/**
 * Provides a series of services in order to handle the configuration for phpDocumentor.
 *
 * This class is responsible for registering a 'Merger' service that is used to combine several configuration
 * definitions into one and will add a new option `config` to all commands of phpDocumentor.
 *
 * Exposed services:
 *
 * - 'config', the configuration service containing all options and parameters for phpDocumentor.
 * - 'config.merger', a service used to combine the configuration template with the user configuration file (phpdoc.xml
 *   of phpdoc.dist.xml).
 *
 * The following variables are exposed:
 *
 * - 'config.path.template', the location of the configuration template with defaults.
 * - 'config.path.user', the location of the user configuration file that will be merged with the template.
 * - 'config.class', the class name of the root configuration object.
 */
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
        $this->addMerger($app);

        $app->extend(
            'console',
            function (ConsoleApplication $console) {
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

        $app['config.path.template'] = __DIR__ . '/Resources/phpdoc.tpl.xml';
        $app['config.path.user'] = getcwd()
            . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml');
        $app['config.class'] = 'phpDocumentor\Configuration';

        $app['config'] = $app->share(
            function ($app) {
                $loader = new Loader($app['serializer'], $app['config.merger']);

                return $loader->load($app['config.path.template'], $app['config.path.user'], $app['config.class']);
            }
        );
    }

    /**
     * Initializes and adds the configuration merger object as the 'config.merger' service to the container.
     *
     * @param Application $container
     *
     * @return void
     */
    private function addMerger(Application $container)
    {
        $this->addMergerAnnotations($container);

        $container['config.merger'] = $container->share(
            function () {
                return new Merger(new AnnotationReader());
            }
        );
    }

    /**
     * Adds the annotations for the Merger component to the Serializer.
     *
     * @param Application $container
     *
     * @throws \RuntimeException if the annotation handler for Jms Serializer is not added to the container as
     *   'serializer.annotations' service.
     *
     * @return void
     */
    private function addMergerAnnotations(Application $container)
    {
        if (!isset($container['serializer.annotations'])) {
            throw new \RuntimeException(
                'The configuration service provider depends on the JmsSerializer Service Provider but the '
                . '"serializer.annotations" key could not be found in the container.'
            );
        }

        $annotations = $container['serializer.annotations'];
        $annotations[] = array(
            'namespace' => 'phpDocumentor\Configuration\Merger\Annotation',
            'path' => __DIR__ . '/../../'
        );
        $container['serializer.annotations'] = $annotations;
    }
}
