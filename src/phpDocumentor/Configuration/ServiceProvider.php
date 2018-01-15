<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use Doctrine\Common\Annotations\AnnotationReader;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Input\ArgvInput;

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
     * @param Container $app An Application instance
     */
    public function register(Container $app)
    {
        $app['config.path.template'] = __DIR__ . '/Resources/phpdoc.tpl.xml';
        $app['config.path.user'] = getcwd()
            . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml');
        $app['config.class'] = 'phpDocumentor\Configuration';

        $app['config'] = function ($app) {
            $configFile = (new ArgvInput())->getParameterOption(['--configuration', '-c'], $app['config.path.user']);

            $loader = new Loader($app['serializer'], new Merger(new AnnotationReader()));

            return $loader->load($app['config.path.template'], $configFile, $app['config.class']);
        };
    }
}
