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

/**
 * Test class for \phpDocumentor\Translator.
 *
 * @covers phpDocumentor\Translator
 */
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    const TEST_KEY = 'KEY';

    const TEST_VALUE = 'value';

    const LOCALE_NL = 'nl';

    /** @var Translator $fixture */
    protected $fixture = null;

    /** @var string Name of the file where an example translation file is stored. */
    protected $filename;

    /** @var string Name of the file where an example domain specific translation file is stored. */
    protected $domainFilename;

    /**
     * Instantiates a new Translator for use as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new Translator();

        $this->filename = sys_get_temp_dir() . '/en.php';
        $this->domainFilename = sys_get_temp_dir() . '/domain.en.php';
        file_put_contents(
            $this->filename,
            <<<TRANSLATION_FILE
<?php
return array(
    'KEY' => 'value',
);
TRANSLATION_FILE
        );
        file_put_contents(
            $this->domainFilename,
            <<<TRANSLATION_FILE
<?php
return array(
    'KEY' => 'domain',
);
TRANSLATION_FILE
        );
    }

    /**
     * Destroys the test translation file.
     *
     * @return void
     */
    protected function tearDown()
    {
        unlink($this->filename);
    }

    /**
     * @covers phpDocumentor\Translator::translate
     */
    public function testTranslationReturnsKeyForUnknownTranslation()
    {
        $this->assertEquals(self::TEST_KEY, $this->fixture->translate(self::TEST_KEY));
    }

    /**
     * @covers phpDocumentor\Translator::addTranslations
     * @covers phpDocumentor\Translator::translate
     */
    public function testCanUseTranslationFromIndividualFile()
    {
        $this->fixture->addTranslations($this->filename);

        $this->assertEquals(self::TEST_VALUE, $this->fixture->translate(self::TEST_KEY));
    }

    /**
     * @covers phpDocumentor\Translator::addTranslations
     * @covers phpDocumentor\Translator::translate
     */
    public function testCanUseTranslationFromIndividualFileWithAlternateLocale()
    {
        $this->fixture->addTranslations($this->filename, self::LOCALE_NL);

        $this->assertEquals(
            self::TEST_VALUE,
            $this->fixture->translate(self::TEST_KEY, Translator::DEFAULT_DOMAIN, self::LOCALE_NL)
        );
    }

    /**
     * @covers phpDocumentor\Translator::addTranslations
     * @covers phpDocumentor\Translator::translate
     */
    public function testTranslateWithAlternateDefaultLocale()
    {
        $this->fixture->addTranslations($this->filename, self::LOCALE_NL);
        $this->fixture->setLocale(self::LOCALE_NL);

        $this->assertEquals(self::TEST_VALUE, $this->fixture->translate(self::TEST_KEY));
    }

    /**
     * @covers phpDocumentor\Translator::addTranslations
     * @covers phpDocumentor\Translator::translate
     */
    public function testCanUseTranslationFromIndividualFileWithFallbackToEnglish()
    {
        $this->fixture->addTranslations($this->filename, Translator::DEFAULT_LOCALE);

        $this->assertEquals(
            self::TEST_VALUE,
            $this->fixture->translate(self::TEST_KEY, Translator::DEFAULT_DOMAIN, self::LOCALE_NL)
        );
    }

    /**
     * @covers phpDocumentor\Translator::addTranslationFolder
     * @covers phpDocumentor\Translator::translate
     */
    public function testCanUseTranslationFromFolder()
    {
        $this->fixture->addTranslationFolder(dirname($this->filename));

        $this->assertEquals(self::TEST_VALUE, $this->fixture->translate(self::TEST_KEY));
    }

    /**
     * @covers phpDocumentor\Translator::addTranslationFolder
     * @covers phpDocumentor\Translator::translate
     */
    public function testCanUseTranslationsFromASpecificDomainOrCatalogue()
    {
        $this->fixture->addTranslationsUsingPattern(dirname($this->domainFilename), 'domain');

        $this->assertEquals('domain', $this->fixture->translate(self::TEST_KEY, 'domain'));
    }
}
