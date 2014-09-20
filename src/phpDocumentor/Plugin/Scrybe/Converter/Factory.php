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

/**
 * This factory attempts to create a converter given an input and output format and return that.
 *
 * <code>
 *     use phpDocumentor\Plugin\Scrybe\Converter\ConverterFactory;
 *     use phpDocumentor\Plugin\Scrybe\Converter\Format\Format;
 *
 *     $converter_factory = new ConverterFactory();
 *     $converter = $converter_factory->get(
 *         Format::MARKDOWN, Format::HTML
 *     );
 * <code>
 *
 * @author Mike van Riel <mike.vanriel@naenius.com>
 */
class Factory
{
    /** @var Definition\Factory */
    protected $definition_factory = null;

    /** @var ConverterInterface[] */
    protected $converters         = array();

    /** @var Logger */
    protected $logger;

    /**
     * Constructs a new factory.
     *
     * A Definition\Factory may optionally be passed to provide an alternate method of creating Definitions or to
     * construct the Definition\Factory with a different Format\Collection to influence the possible options.
     *
     * @param string[]                $converters
     * @param Definition\Factory|null $definition_factory
     * @param Logger                  $logger
     */
    public function __construct(array $converters, Definition\Factory $definition_factory, Logger $logger)
    {
        if ($definition_factory === null) {
            $definition_factory = $this->getDefaultDefinitionFactory();
        }

        $this->converters = $converters;
        $this->setDefinitionFactory($definition_factory);
        $this->logger = $logger;
    }

    /**
     * Retrieves a new instance of the converter necessary to convert the give input format to the given output format.
     *
     * @param string $input_format
     * @param string $output_format
     *
     * @throws Exception\ConverterNotFoundException
     *
     * @return ConverterInterface
     */
    public function get($input_format, $output_format)
    {
        $definition = $this->definition_factory->get($input_format, $output_format);

        foreach ($this->converters as $class => $formats) {
            if (array($input_format, $output_format) == $formats) {
                $assets = new Metadata\Assets();
                $assets->setLogger($this->logger);

                $toc      = new Metadata\TableOfContents();
                $glossary = new Metadata\Glossary();

                /** @var ConverterInterface $converter */
                $converter = new $class($definition, $assets, $toc, $glossary);
                $converter->setLogger($this->logger);

                return $converter;
            }
        }

        throw new Exception\ConverterNotFoundException(
            'No converter could be found to convert from ' . $input_format . ' to ' . $output_format
        );
    }

    /**
     * Returns a list of supported input formats for the given output format.
     *
     * @param string $given_output_format A format definition per the constants in the Format class.
     *
     * @return string[] An array of format definitions per the constantst in the Format class.
     */
    public function getSupportedInputFormats($given_output_format)
    {
        $result = array();
        foreach ($this->converters as $formats) {
            list($input_format, $output_format) = $formats;
            if ($given_output_format == $output_format) {
                $result[] = $input_format;
            }
        }

        return $result;
    }

    /**
     * Sets the converters for this Factory.
     *
     * @param ConverterInterface[] $converters
     *
     * @return void
     */
    public function setConverters(array $converters)
    {
        $this->converters = $converters;
    }

    /**
     * Method used to retrieve the default Definition Factory.
     *
     * This is used when the user has not provided their own definition factory in the constructor.
     *
     * @see __construct() where this method is used.
     *
     * @return Definition\Factory
     */
    protected function getDefaultDefinitionFactory()
    {
        return new Definition\Factory(new Format\Collection());
    }

    /**
     * Sets the Definition Factory used to retrieve definitions from.
     *
     * @param Definition\Factory $definition_factory
     *
     * @return void
     */
    protected function setDefinitionFactory(Definition\Factory $definition_factory)
    {
        $this->definition_factory = $definition_factory;
    }
}
