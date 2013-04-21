<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Zend\I18n\Translator\Translator as ZendTranslator;

/**
 * Translator adapter for phpDocumentor.
 *
 * This class encapsulates (or actually extends) a Translator object that can be used to translate messages from the
 * fallback language to another.
 *
 * This encapsulation serves two purposes;
 *
 * 1. To make a migration to another translator easier if necessary
 * 2. To fix a bug in Zend\I18n\Translator\Translator where the cache is not cleared when new messages are added.
 *
 * Due to issue 2 this class extends the Zend Translator and does not properly encapsulate it.
 */
class Translator extends ZendTranslator
{
    public function addTranslationFile($type, $filename, $textDomain = 'default', $locale = null)
    {
        parent::addTranslationFile($type, $filename, $textDomain, $locale);
        $this->messages = array();

        return $this;
    }

    public function addTranslationFilePattern($type, $baseDir, $pattern, $textDomain = 'default')
    {
        parent::addTranslationFilePattern($type, $baseDir, $pattern, $textDomain);
        $this->messages = array();

        return $this;
    }

    public function translate($message, $textDomain = 'default', $locale = null)
    {
        return parent::translate($message, $textDomain, $locale);
    }
}
