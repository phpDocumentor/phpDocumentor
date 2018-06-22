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

namespace phpDocumentor\Plugin\Scrybe\Converter;

use Monolog\Logger;
use phpDocumentor\Plugin\Scrybe\Converter\Format\Collection;
use phpDocumentor\Plugin\Scrybe\Converter\Metadata\Assets;
use phpDocumentor\Plugin\Scrybe\Converter\Metadata\Glossary;
use phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;
use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;

/**
 * This interface provides a basic contract between the Converters and all classes that want to use them.
 */
interface ConverterInterface
{
    /**
     * Standard option used to convey the name of the template to use.
     *
     * @see \phpDocumentor\Plugin\Scrybe\Command\Manual\ConvertCommandAbstract::execute()
     */
    const OPTION_TEMPLATE = 'template';

    /**
     * Converts the given $source using the formats that belong to this
     * converter.
     *
     * This method will return null unless the 'scrybe://result' is used.
     *
     * @param Collection        $source      Collection of input files.
     * @param TemplateInterface $template Template used to decorate the
     *     output with.
     *
     * @see DESTINATION_RESULT to use as destination to return data.
     */
    public function convert(Collection $source, TemplateInterface $template): ?string;

    /**
     * Returns the definition for this Converter.
     */
    public function getDefinition(): Definition\Definition;

    /**
     * Sets an option which can optionally be used in converters.
     */
    public function setOption(string $name, string $value): void;

    /**
     * Returns the AssetManager that keep track of which assets are used.
     */
    public function getAssets(): Assets;

    /**
     * Returns the table of contents object that keeps track of all
     * headings and their titles.
     */
    public function getTableOfContents(): TableOfContents;

    /**
     * Returns the glossary object that keeps track of all the glossary terms
     * that have been provided.
     */
    public function getGlossary(): Glossary;

    /**
     * Optionally set a logger for this converter.
     */
    public function setLogger(Logger $logger): void;
}
