<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe;

use phpDocumentor\Plugin\Scrybe\Converter\Definition\Factory;
use phpDocumentor\Plugin\Scrybe\Converter\Format\Format;

/**
 * Creates and binds the components for the generation of manuals.
 *
 * Scrybe is a plugin that allows authors to write documentation in a markup format of their choosing and generate
 * human-readable documentation from it.
 */
class ServiceProvider
{
    const CONVERTER_FACTORY = 'converter-factory';

    const TEMPLATE_FACTORY = 'template-factory';

    const CONVERTER_DEFINITION_FACTORY = 'converter_definition_factory';

    const FORMATS = 'converter_formats';

    const CONVERTERS = 'converters';

    const TEMPLATE_FOLDER = 'template_folder';

    /**
     * Registers services on the given app.
     *
     * @param Container $app An Application instance.
     */
    public function register(Container $app): void
    {
        $app[self::TEMPLATE_FOLDER] = __DIR__ . '/data/templates/';
        $app[self::CONVERTERS] = [
            '\phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\ToHtml' => [Format::RST, Format::HTML],
        ];

        $app[self::FORMATS] = function () {
            return new Converter\Format\Collection();
        };

        $app[self::CONVERTER_DEFINITION_FACTORY] = function ($container) {
            return new Factory($container[self::FORMATS]);
        };

        $app[self::CONVERTER_FACTORY] = function ($container) {
            return new Converter\Factory(
                $container['converters'],
                $container['converter_definition_factory'],
                $container['monolog']
            );
        };

        $app[self::TEMPLATE_FACTORY] = function ($app) {
            return new Template\Factory(
                ['twig' => new Template\Twig($app[self::TEMPLATE_FOLDER])]
            );
        };

        $this->addCommands($app);
    }

    /**
     * Method responsible for adding the commands for this application.
     */
    protected function addCommands(Container $app): void
    {
        if ($app instanceof Application) {
            $app->command(
                new Command\Manual\ToHtmlCommand(null, $app[self::TEMPLATE_FACTORY], $app[self::CONVERTER_FACTORY])
            );
        }

        // FIXME: Disabled the ToLatex and ToPdf commands for now to prevent confusion of users.
        // $this->command(new \phpDocumentor\Plugin\Scrybe\Command\Manual\ToLatexCommand());
        // $this->command(new \phpDocumentor\Plugin\Scrybe\Command\Manual\ToPdfCommand());
    }
}
