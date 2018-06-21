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
     */
    public function setTranslator(Translator $translator);
}
