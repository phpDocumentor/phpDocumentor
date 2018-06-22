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
use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;
use SplFileInfo;

abstract class BaseConverter implements ConverterInterface
{
    /** @var Definition\Definition */
    protected $definition = null;

    /** @var string[] */
    protected $options = [];

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
     */
    public function __construct(
        Definition\Definition $definition,
        Metadata\Assets $assets,
        Metadata\TableOfContents $tableOfContents,
        Metadata\Glossary $glossary
    ) {
        $this->definition = $definition;
        $this->assets = $assets;
        $this->toc = $tableOfContents;
        $this->glossary = $glossary;
    }

    /**
     * Set a logger for this converter.
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Returns the AssetManager that keep track of which assets are used.
     */
    public function getAssets(): Metadata\Assets
    {
        return $this->assets;
    }

    /**
     * Returns the table of contents object that keeps track of all
     * headings and their titles.
     */
    public function getTableOfContents(): Metadata\TableOfContents
    {
        return $this->toc;
    }

    /**
     * Returns the glossary object that keeps track of all the glossary terms
     * that have been provided.
     */
    public function getGlossary(): Metadata\Glossary
    {
        return $this->glossary;
    }

    /**
     * Returns the definition for this Converter.
     */
    public function getDefinition(): Definition\Definition
    {
        return $this->definition;
    }

    /**
     * Sets an option with the given name.
     */
    public function setOption(string $name, string $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * Returns the option with the given name or null if the option does not
     * exist.
     */
    public function getOption(string $name): ?string
    {
        return $this->options[$name] ?? null;
    }

    /**
     * Configures and initializes the subcomponents specific to this converter.
     */
    public function configure(): void
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
     */
    abstract protected function discover(): void;

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
     * @see manual://extending#build_cycle for more information regarding the
     * build process.
     * @return string[]|null The contents of the resulting file(s) or null if
     * the files are written directly to file.
     */
    abstract protected function create(TemplateInterface $template): ?string;

    /**
     * Converts the given $source using the formats that belong to this
     * converter.
     *
     * This method will return null unless the 'scrybe://result' is used.
     *
     * @param Collection        $source      Collection of input files.
     * @param TemplateInterface $template Template used to decorate the
     *     output with.
     */
    public function convert(Collection $source, TemplateInterface $template): ?string
    {
        $this->fileset = $source;
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
     */
    protected function addTemplateAssets(TemplateInterface $template): void
    {
        /** @var SplFileInfo $file_info */
        foreach ($template->getAssets() as $filename => $file_info) {
            $this->assets->set($filename, $file_info->getRelativePathname());
        }
    }

    /**
     * Returns the filename used for the output path.
     */
    protected function getDestinationFilename(Metadata\TableOfContents\File $file): string
    {
        return $this->definition->getOutputFormat()->convertFilename($file->getRealPath());
    }

    /**
     * Returns the filename relative to the Project Root of the fileset.
     */
    public function getDestinationFilenameRelativeToProjectRoot(Metadata\TableOfContents\File $file): string
    {
        return substr($this->getDestinationFilename($file), strlen($this->fileset->getProjectRoot()));
    }

    /**
     * Returns the logger for this converter.
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }
}
