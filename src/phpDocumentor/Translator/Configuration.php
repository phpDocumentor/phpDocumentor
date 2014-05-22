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

namespace phpDocumentor\Translator;

use JMS\Serializer\Annotation as Serializer;

/**
 * Configuration definition for the translations.
 */
class Configuration
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $locale = 'en';

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
