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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText;

use Exception;
use phpDocumentor\Plugin\Scrybe\Converter\BaseConverter;
use phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\File;
use phpDocumentor\Plugin\Scrybe\Converter\ToHtmlInterface;
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
     * @throws Exception
     * @see manual://internals#build_cycle for more information regarding the build process.
     */
    protected function discover(): void
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
            } catch (Exception $e) {
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
     * @see manual://internals#build_cycle for more information regarding the build process.
     * @return string[]|null The contents of the resulting file(s) or null if the files are written directly to file.
     */
    protected function create(TemplateInterface $template): ?string
    {
        $result = [];

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
     * @see BaseConverter::$options for where the 'root' variable is set.
     */
    protected function setDestinationRoot(string $destination): void
    {
        $this->options['root'] = dirname($destination) !== '.'
            ? implode('/', array_fill(0, count(explode('/', dirname($destination))), '..')) . '/'
            : './';
    }
}
