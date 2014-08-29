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
use phpDocumentor\Fileset\File;
use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;
use Symfony\Component\Finder\SplFileInfo;

abstract class BaseConverter implements ConverterInterface
{
    /** @var Definition\Definition */
    protected $definition = null;

    /** @var string[] */
    protected $options = array();

    /** @var Collection */
    protected $fileset;

    /** @var Metadata\Assets */
    protected $assets;

    /** @var Metadata\TableOfContents */
    protected $toc;

    /** @var Metadata\Glossary */
    protected $glossary;

    /** @var Logger */
    protected $logger;

    /**
     * Initializes this converter and sets the definition.
     *
     * @param Definition\Definition $definition
     */
    public function __construct(
        Definition\Definition $definition,
        Metadata\Assets $assets,
        Metadata\TableOfContents $tableOfContents,
        Metadata\Glossary $glossary
    ) {
        $this->definition = $definition;
        $this->assets     = $assets;
        $this->toc        = $tableOfContents;
        $this->glossary   = $glossary;
    }

    /**
     * Set a logger for this converter.
     *
     * @param Logger $logger
     *
     * @return void
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns the AssetManager that keep track of which assets are used.
     *
     * @return \phpDocumentor\Plugin\Scrybe\Converter\Metadata\Assets
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Returns the table of contents object that keeps track of all
     * headings and their titles.
     *
     * @return \phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents
     */
    public function getTableOfContents()
    {
        return $this->toc;
    }

    /**
     * Returns the glossary object that keeps track of all the glossary terms
     * that have been provided.
     *
     * @return \phpDocumentor\Plugin\Scrybe\Converter\Metadata\Glossary
     */
    public function getGlossary()
    {
        return $this->glossary;
    }

    /**
     * Returns the definition for this Converter.
     *
     * @return Definition\Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Sets an option with the given name.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Returns the option with the given name or null if the option does not
     * exist.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Configures and initializes the subcomponents specific to this converter.
     *
     * @return void
     */
    public function configure()
    {
    }

    /**
     * Discovers the data that is spanning all files.
     *
     * This method tries to find any data that needs to be collected before
     * the actual creation and substitution phase begins.
     *
     * Examples of data that needs to be collected during an initial phase is
     * a table of contents, list of document titles for references, assets
     * and more.
     *
     * @see manual://extending#build_cycle for more information regarding the
     *     build process.
     *
     * @return void
     */
    abstract protected function discover();

    /**
     * Converts the input files into one or more output files in the intended
     * format.
     *
     * This method reads the files, converts them into the correct format and
     * returns the contents of the conversion.
     *
     * The template is used to decorate the individual files and can be obtained
     * using the `\phpDocumentor\Plugin\Scrybe\Template\Factory` class.
     *
     * @param TemplateInterface $template
     *
     * @see manual://extending#build_cycle for more information regarding the
     *     build process.
     *
     * @return string[]|null The contents of the resulting file(s) or null if
     *     the files are written directly to file.
     */
    abstract protected function create(TemplateInterface $template);

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
     * @return string[]|null
     */
    public function convert(Collection $source, TemplateInterface $template)
    {
        $this->fileset      = $source;
        $this->assets->setProjectRoot($this->fileset->getProjectRoot());

        $template->setExtension(current($this->definition->getOutputFormat()->getExtensions()));

        $this->configure();
        $this->discover();

        $this->addTemplateAssets($template);
        $this->setOption('toc', $this->toc);

        return $this->create($template);
    }

    /**
     * Adds the assets of the template to the Assets manager.
     *
     * @param TemplateInterface $template
     *
     * @return void
     */
    protected function addTemplateAssets($template)
    {
        /** @var SplFileInfo $file_info */
        foreach ($template->getAssets() as $filename => $file_info) {
            $this->assets->set($filename, $file_info->getRelativePathname());
        }
    }

    /**
     * Returns the filename used for the output path.
     *
     * @param File $file
     *
     * @return string
     */
    protected function getDestinationFilename(File $file)
    {
        return $this->definition->getOutputFormat()->convertFilename($file->getRealPath());
    }

    /**
     * Returns the filename relative to the Project Root of the fileset.
     *
     * @param File $file
     *
     * @return string
     */
    public function getDestinationFilenameRelativeToProjectRoot(File $file)
    {
        return substr($this->getDestinationFilename($file), strlen($this->fileset->getProjectRoot()));
    }

    /**
     * Returns the logger for this converter.
     *
     * @return \Monolog\Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}
