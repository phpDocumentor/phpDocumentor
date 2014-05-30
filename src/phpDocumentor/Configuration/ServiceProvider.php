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
     * @param Application $app
     * @return Application
     */
    private function addMerger(Application $app)
    {
        if (!isset($app['serializer.annotations'])) {
            throw new \RuntimeException(
                'The configuration service provider depends on the JmsSerializer Service Provider but the '
                . '"serializer.annotations" key could not be found in the container.'
            );
        }

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
    }
}