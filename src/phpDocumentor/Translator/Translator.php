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

use Zend\I18n\Translator\Translator as ZendTranslator;

/**
 * Translator proxy for phpDocumentor.
 *
 * This class encapsulates (or actually extends) a Translator object that can be used to translate messages from the
 * fallback language to another.
 *
 * This encapsulation serves two purposes;
 *
 * 1. To make a migration to another translator easier if necessary
 * 2. To fix a bug in Zend\I18n\Translator\Translator where the cache is not cleared when new messages are added.
 *
 * Due to issue 2 this class extends the Zend Translator and does not use composition to proxy calls to the translator;
 * as such it is not recommended to use any public function not defined in this proxy as it may be removed.
 *
 * Before invoking the {@see self::translate()} method the user must first load a series of translation messages in the
 * desired locale; this can be done by invoking the {@see self::addTranslationFile()} or
 * {@see self::addTranslationFolder()} methods. These try to include a file containing a plain PHP Array and merge that
 * with the translation table of this translator.
 *
 * An example of a translation file can be:
 *
 * ```
 * return array(
 *     'KEY' => 'translated message',
 * );
 * ```
 */
class Translator extends ZendTranslator
{
    /**
     * The translation file type.
     *
     * This type is hardcoded into a constant to simplify the signature of the addTranslationFile and
     * addTranslationFilePattern methods. This will simplify the migration to another component in the future as an
     * incompatibility between two libraries may emerge due to differing types or typenames.
     *
     * This translator class may be used by plugin developers to have translating elements in their plugins; as such
     * the signatures here are considered to be stable / api.
     * @var string
     */
    const TRANSLATION_FILE_TYPE = 'phparray';

    /** @var string Represents the default locale for phpDocumentor */
    const DEFAULT_LOCALE = 'en';

    /** @var string Translation strings may be divided into 'domains', this is the default domain */
    const DEFAULT_DOMAIN = 'default';

    /** @var string the default name of files loaded by {@see self::addTranslationFolder()} */
    const DEFAULT_PATTERN = '%s.php';

    /**
     * Pre-set the translator with the default locale as fallback.
     */
    public function __construct()
    {
        $this->setLocale(self::DEFAULT_LOCALE);
        $this->setFallbackLocale(self::DEFAULT_LOCALE);
    }

    /**
     * Sets the default locale to use when translating messages.
     *
     * @param string $locale
     *
     * @api
     *
     * @return Translator
     */
    public function setLocale($locale)
    {
        return parent::setLocale($locale);
    }

    /**
     * Adds a translation file for a specific locale, or the default locale when none is provided.
     *
     * @param string      $filename   Name of the file to add.
     * @param string|null $locale     The locale to assign to, matches
     *     {@link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes ISO-639-1} and defaults to en (English).
     * @param string      $textDomain Translations may be divided into separate files / domains; this represents in
     *     which domain the translation should be.
     *
     * @api
     *
     * @return $this
     */
    public function addTranslations($filename, $locale = self::DEFAULT_LOCALE, $textDomain = self::DEFAULT_DOMAIN)
    {
        parent::addTranslationFile(self::TRANSLATION_FILE_TYPE, $filename, $textDomain, $locale);

        $this->messages = array();

        return $this;
    }

    /**
     * Adds a folder with files containing translation sources.
     *
     * This method scans the provided folder for any file matching the following format:
     *
     *     `[domain].[locale].php`
     *
     * If the domain matches the {@see self::DEFAULT_DOMAIN default domain} then that part is omitted and the filename
     * should match:
     *
     *     `[locale].php`
     *
     * @link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes for a list of ISO-639-1 locale codes as used by
     *     this method.
     *
     * @param string   $folder  Name of the folder, it is recommended to use an absolute path.
     * @param string[] $domains One or more domains to load, when none is provided only the default is added.
     *
     * @api
     *
     * @return $this
     */
    public function addTranslationFolder($folder, array $domains = array())
    {
        if (empty($domains)) {
            $domains = array(self::DEFAULT_DOMAIN);
        }

        foreach ($domains as $domain) {
            $this->addTranslationsUsingPattern($folder, $domain);
        }

        return $this;
    }

    /**
     * Adds a series of translation files given a pattern.
     *
     * This method will search the base directory for a series of files matching the given pattern (where %s is replaces
     * by the two-letter locale shorthand) and adds any translations to the translation table.
     *
     * @param string $baseDir    Directory to search in (not-recursive)
     * @param string $textDomain The domain to assign the translation messages to.
     * @param string $pattern    The pattern used to load files for all languages, one variable `%s` is supported and
     *     is replaced with the {@link http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes ISO-639-1 code} for each
     *     locale that is requested by the translate method.
     *
     * @internal this method is not to be used by consumers; it is an extension of the Zend Translator component
     *     and is overridden to clear the messages caching array so it may be rebuild.
     *
     * @see self::addTranslationFolder() to provide a series of translation files.
     *
     * @return $this|ZendTranslator
     */
    public function addTranslationsUsingPattern(
        $baseDir,
        $textDomain = self::DEFAULT_DOMAIN,
        $pattern = self::DEFAULT_PATTERN
    ) {
        if ($textDomain !== self::DEFAULT_DOMAIN && $pattern === self::DEFAULT_PATTERN) {
            $pattern = $textDomain . '.' . $pattern;
        }

        parent::addTranslationFilePattern(self::TRANSLATION_FILE_TYPE, $baseDir, $pattern, $textDomain);

        $this->messages = array();

        return $this;
    }

    /**
     * Attempts to translate the given message or code into the provided locale.
     *
     * @param string $message    The message or code to translate.
     * @param string $textDomain A message may be located in a domain, here you can provide in which.
     * @param null   $locale     The locale to translate to or the default if not set.
     *
     * @return string
     */
    public function translate($message, $textDomain = self::DEFAULT_DOMAIN, $locale = null)
    {
        return parent::translate($message, $textDomain, $locale);
    }
}
