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

namespace phpDocumentor\Translator;

use Cilex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use phpDocumentor\Configuration as ApplicationConfiguration;

/**
 * Registers all components for the translator to work.
 *
 * This provider registers the following service:
 *
 * - translator, provides translation services
 *
 * In addition to the above service the following parameters are registered as well:
 *
 * - translator.locale, contains the current locale.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers the translator using the currently active locale.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        /** @var ApplicationConfiguration $config */
        $config = $app['config'];

        $app['translator.locale'] = $config->getTranslator()->getLocale();

        $app['translator'] = function ($app) {
            $translator = new Translator();
            $translator->setLocale($app['translator.locale']);

            return $translator;
        };
    }
}
