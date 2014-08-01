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

namespace phpDocumentor\Plugin\Scrybe\Converter;

use Monolog\Logger;
use phpDocumentor\Fileset\Collection;
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
     *
     * @return string[]|null
     */
    public function convert(Collection $source, TemplateInterface $template);

    /**
     * Returns the definition for this Converter.
     *
     * @return Definition\Definition
     */
    public function getDefinition();

    /**
     * Sets an option which can optionally be used in converters.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setOption($name, $value);

    /**
     * Returns the AssetManager that keep track of which assets are used.
     *
     * @return Assets
     */
    public function getAssets();

    /**
     * Returns the table of contents object that keeps track of all
     * headings and their titles.
     *
     * @return TableOfContents
     */
    public function getTableOfContents();

    /**
     * Returns the glossary object that keeps track of all the glossary terms
     * that have been provided.
     *
     * @return Glossary
     */
    public function getGlossary();

    /**
     * Optionally set a logger for this converter.
     *
     * @param Logger $logger
     *
     * @return void
     */
    public function setLogger(Logger $logger);
}
