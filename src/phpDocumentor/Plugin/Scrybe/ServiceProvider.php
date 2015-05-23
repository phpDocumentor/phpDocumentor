<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Plugin\Scrybe\Converter\Definition\Factory;
use phpDocumentor\Plugin\Scrybe\Converter\Format\Format;
use phpDocumentor\Plugin\Scrybe\Descriptor\Builder\DocbookAssembler;
use phpDocumentor\Plugin\Scrybe\Parser\Backend\Document;

/**
 * Creates and binds the components for the generation of manuals.
 *
 * Scrybe is a plugin that allows authors to write documentation in a markup format of their choosing and generate
 * human-readable documentation from it.
 */
class ServiceProvider implements ServiceProviderInterface
{
    const CONVERTER_FACTORY            = 'converter-factory';
    const TEMPLATE_FACTORY             = 'template-factory';
    const CONVERTER_DEFINITION_FACTORY = 'converter_definition_factory';
    const FORMATS                      = 'converter_formats';
    const CONVERTERS                   = 'converters';
    const TEMPLATE_FOLDER              = 'template_folder';

    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        $this->registerAssemblers($app);

        $app['parser'] = $app->extend(
            'parser',
            function ($parser) use ($app) {
                return $parser->registerBackend(new Document($app['descriptor.analyzer']));
            }
        );

        $app[self::TEMPLATE_FOLDER] = realpath(__DIR__ . '/data/templates/');
        $app[self::CONVERTERS] = array(
            '\phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\ToHtml' => array(Format::RST, Format::HTML),
        );

        $app[self::FORMATS] = $app->share(
            function () {
                return new Converter\Format\Collection();
            }
        );
        $app[self::CONVERTER_DEFINITION_FACTORY] = $app->share(
            function ($container) {
                return new Factory($container[ServiceProvider::FORMATS]);
            }
        );
        $app[self::CONVERTER_FACTORY] = $app->share(
            function ($container) {
                return new Converter\Factory(
                    $container['converters'],
                    $container['converter_definition_factory']
                );
            }
        );
        $app[self::TEMPLATE_FACTORY]  = $app->share(
            function ($app) {
                return new Template\Factory(
                    array('twig' => new Template\Twig($app[ServiceProvider::TEMPLATE_FOLDER]))
                );
            }
        );

        $this->addCommands($app);
    }

    /**
     * Method responsible for adding the commands for this application.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addCommands(Application $app)
    {
        $app->command(
            new Command\Manual\ToHtmlCommand(null, $app[self::TEMPLATE_FACTORY], $app[self::CONVERTER_FACTORY])
        );

        // FIXME: Disabled the ToLatex and ToPdf commands for now to prevent confusion of users.
        // $this->command(new \phpDocumentor\Plugin\Scrybe\Command\Manual\ToLatexCommand());
        // $this->command(new \phpDocumentor\Plugin\Scrybe\Command\Manual\ToPdfCommand());
    }

    /**
     * @param Application $app
     */
    private function registerAssemblers(Application $app)
    {
        $app->extend('descriptor.builder.initializers', function ($chain) {
            /** @var InitializerChain $chain */
            $chain->addInitializer(function (Analyzer $analyzer) {
                $factory = $analyzer->getAssemblerFactory();
                $factory->register(
                    function ($criteria) {
                        return $criteria instanceof \ezcDocumentDocbook;
                    },
                    new DocbookAssembler()
                );
            });

            return $chain;
        });
    }
}
