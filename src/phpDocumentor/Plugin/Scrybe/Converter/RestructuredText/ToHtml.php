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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText;

use phpDocumentor\Plugin\Scrybe\Converter\ToHtmlInterface;
use phpDocumentor\Fileset\File;
use phpDocumentor\Plugin\Scrybe\Converter\BaseConverter;
use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;

/**
 * Class used to convert one or more RestructuredText documents to their HTML representation.
 *
 * This class uses a two-phase process to interpret and parse the RestructuredText documents, namely Discovery
 * and Creation.
 *
 * @see manual://internals for a detailed description of the process.
 */
class ToHtml extends BaseConverter implements ToHtmlInterface
{
    /**
     * Discovers the data that is spanning all files.
     *
     * This method tries to find any data that needs to be collected before the actual creation and substitution
     * phase begins.
     *
     * Examples of data that needs to be collected during an initial phase is a table of contents, list of document
     * titles for references, assets and more.
     *
     * @see manual://internals#build_cycle for more information regarding the build process.
     *
     * @return void
     */
    protected function discover()
    {
        /** @var File $file */
        foreach ($this->fileset as $file) {
            $rst = new Document($this, $file);
            $rst->options->xhtmlVisitor = 'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Discover';

            if ($this->getLogger()) {
                $this->getLogger()->info('Scanning file "' . $file->getRealPath() . '"');
            }

            try {
                $rst->getAsXhtml();
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Converts the input files into one or more output files in the intended format.
     *
     * This method reads the files, converts them into the correct format and returns the contents of the conversion.
     *
     * The template is provided using the $template parameter and is used to decorate the individual files. It can be
     * obtained using the `\phpDocumentor\Plugin\Scrybe\Template\Factory` class.
     *
     * @param TemplateInterface $template
     *
     * @see manual://internals#build_cycle for more information regarding the build process.
     *
     * @return string[]|null The contents of the resulting file(s) or null if the files are written directly to file.
     */
    protected function create(TemplateInterface $template)
    {
        $result = array();

        /** @var File $file */
        foreach ($this->fileset as $file) {
            $rst = new Document($this, $file);
            $destination = $this->getDestinationFilenameRelativeToProjectRoot($file);
            $this->setDestinationRoot($destination);

            if ($this->getLogger()) {
                $this->getLogger()->info('Parsing file "' . $file->getRealPath() . '"');
            }

            try {
                $xhtml_document = $rst->getAsXhtml();
                $converted_contents = $template->decorate($xhtml_document->save(), $this->options);
                $rst->logStats(null, $this->getLogger());
            } catch (\Exception $e) {
                $rst->logStats($e, $this->getLogger());
                continue;
            }

            $result[$destination] = $converted_contents;
        }

        return $result;
    }

    /**
     * Sets the relative path to the root of the generated contents.
     *
     * Basically this method takes the depth of the given destination and replaces it with `..` unless the destination
     * directory name is `.`.
     *
     * @param string $destination The destination path relative to the target folder.
     *
     * @see $options for where the 'root' variable is set.
     *
     * @return void
     */
    protected function setDestinationRoot($destination)
    {
        $this->options['root'] = dirname($destination) != '.'
            ? implode('/', array_fill(0, count(explode('/', dirname($destination))), '..')) . '/'
            : './';
    }
}
