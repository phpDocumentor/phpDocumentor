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

namespace phpDocumentor\Plugin\Standards;

use Mockery as m;
use Psr\Log\LogLevel;

/**
 * Tests the class that represents a Documentation Standard Rule.
 */
final class RuleTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_REF = 'ref';

    const EXAMPLE_MESSAGE = 'message';

    const EXAMPLE_SEVERITY = 3;

    /** @var Rule */
    private $fixture;

    /** @var string[] */
    private $exampleProperties = array('property' => 'value');

    protected function setUp()
    {
        $this->fixture = new Rule(
            self::EXAMPLE_REF,
            self::EXAMPLE_MESSAGE,
            self::EXAMPLE_SEVERITY,
            $this->exampleProperties
        );
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getRef
     */
    public function testRetrievingTheRef()
    {
        $this->assertSame(self::EXAMPLE_REF, $this->fixture->getRef());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getMessage
     */
    public function testRetrievingTheMessage()
    {
        $this->assertSame(self::EXAMPLE_MESSAGE, $this->fixture->getMessage());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getSeverity
     */
    public function testRetrievingTheSeverity()
    {
        $this->assertSame(self::EXAMPLE_SEVERITY, $this->fixture->getSeverity());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getSeverity
     */
    public function testRetrievingTheSeverityAsLogLevel()
    {
        $this->assertSame(LogLevel::NOTICE, $this->fixture->getSeverityAsLogLevel());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getProperties
     */
    public function testRetrievingTheProperties()
    {
        $this->assertSame($this->exampleProperties, $this->fixture->getProperties());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getExclude
     * @covers phpDocumentor\Descriptor\Validator\Rule::setExclude
     */
    public function testRegisterAndRetrieveExcludedRules()
    {
        // ref needs to be a Ruleset so we overwrite the default fixture
        $this->fixture = new Rule(new Ruleset('name'), 'myMessage');

        $this->assertSame(array(), $this->fixture->getExclude());
        $excluded = array('excluded rule');

        $this->fixture->setExclude($excluded);

        $this->assertSame($excluded, $this->fixture->getExclude());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Rule::__construct
     * @covers phpDocumentor\Descriptor\Validator\Rule::getExcludePattern
     * @covers phpDocumentor\Descriptor\Validator\Rule::setExcludePattern
     */
    public function testRegisterAndRetrieveExcludedFilesAndFolders()
    {
        $this->assertSame(array(), $this->fixture->getExcludePattern());
        $excluded = array('excluded file using a pattern*');

        $this->fixture->setExcludePattern($excluded);

        $this->assertSame($excluded, $this->fixture->getExcludePattern());
    }
}
