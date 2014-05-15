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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Translator\Translator;

/**
 * All writers that have items that should be translated should implement this interface
 */
interface Translatable
{
    /**
     * Returns an instance of the object responsible for translating content.
     *
     * @return Translator
     */
    public function getTranslator();

    /**
     * Sets a new object capable of translating strings on this writer.
     *
     * @param Translator $translator
     *
     * @return void
     */
    public function setTranslator(Translator $translator);
}
