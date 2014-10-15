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

namespace phpDocumentor\Plugin\Scrybe\Converter\Format;

/**
 * A Collection containing all different text-bases file formats that are supported by Scrybe, their mmime-types and
 * available extensions.
 */
class Collection extends \ArrayObject
{
    /**
     * Constructs this collection with a default set of formats if none are given.
     *
     * @param Format[]|null $input
     */
    public function __construct(array $input = null)
    {
        if ($input === null) {
            $input = array(
                Format::HTML     => new Format(Format::HTML, 'text/html', array('html', 'htm')),
                Format::JSON     => new Format(Format::JSON, 'application/json', 'json'),
                Format::LATEX    => new Format(Format::LATEX, 'application/x-latex', 'tex'),
                Format::MARKDOWN => new Format(Format::MARKDOWN, 'text/x-markdown', 'md'),
                Format::PDF      => new Format(Format::PDF, 'application/pdf', 'pdf'),
                Format::RST      => new Format(Format::RST, 'text-x-rst', array('rst', 'txt', 'rest', 'restx')),
            );
        }

        parent::__construct($input, 0, 'ArrayIterator');
    }

    /**
     * Finds a format by the given name or throws an exception if that index is not found.
     *
     * @param string $index
     *
     * @throws Exception\FormatNotFoundException if the given format index was not found.
     *
     * @return Format
     */
    public function offsetGet($index)
    {
        if (!$this->offsetExists($index)) {
            throw new Exception\FormatNotFoundException('Format ' . $index . ' is not known');
        }

        return parent::offsetGet($index);
    }
}
