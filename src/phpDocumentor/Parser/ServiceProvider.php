<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Zend\I18n\Translator\Translator;
use phpDocumentor\Parser\Command\Project\ParseCommand;

/**
 * This provider is responsible for registering the parser component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
     *
     * @throws Exception\MissingDependencyException if the Descriptor Builder is not present.
     *
     * @return void
     */
    public function register(Application $app)
    {
        if (!isset($app['descriptor.builder'])) {
            throw new Exception\MissingDependencyException(
                'The builder object that is used to construct the ProjectDescriptor is missing'
            );
        }

        $app['parser'] = $app->share(
            function () {
                return new Parser();
            }
        );

        /** @var Translator $translator  */
        $translator = $app['translator'];
        $translator->addTranslationFilePattern(
            'phparray',
            __DIR__ . DIRECTORY_SEPARATOR . 'Messages',
            '%s.php'
        );

        $app->command(new ParseCommand($app['descriptor.builder'], $app['parser'], $translator));
    }
}
