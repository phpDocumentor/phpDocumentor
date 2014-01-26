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

class Collection extends \ArrayObject
{
    public function __construct($input = null)
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

    public function offsetGet($index)
    {
        if (!$this->offsetExists($index)) {
            throw new Exception\FormatNotFoundException('Format '.$index.' is not known');
        }

        return parent::offsetGet($index);
    }
}
