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

/**
 * Tests the class that represents a Documentation Standard's Rules (and with it effectively the whole standard).
 */
class RulesetTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_NAME = 'name';
    const EXAMPLE_RULE_NAME = 'ruleName';

    /** @var Ruleset */
    private $fixture;

    /** @var Rule */
    private $rule;

    /**
     * Initializes the fixture and provide for dependencies.
     */
    protected function setUp()
    {
        $this->rule = new Rule(self::EXAMPLE_RULE_NAME, 'message');
        $this->fixture = new Ruleset(self::EXAMPLE_NAME, array($this->rule));
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::__construct
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::getName
     */
    public function testNameIsCorrectlyRegistered()
    {
        $this->assertSame(self::EXAMPLE_NAME, $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::__construct
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::getRules
     */
    public function testRulesAreCorrectlyRegistered()
    {
        $this->assertSame(array($this->rule), $this->fixture->getRules());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::__construct
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::getRule
     */
    public function testASpecificRuleCanBeRetrieved()
    {
        $this->assertSame($this->rule, $this->fixture->getRule(self::EXAMPLE_RULE_NAME));
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::__construct
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::addRule
     */
    public function testNewRuleCanBeAdded()
    {
        $rule = new Rule('ref2', 'message');

        $this->fixture->addRule($rule);

        $this->assertSame(array($this->rule, $rule), $this->fixture->getRules());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::getDescription
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::setDescription
     */
    public function testRegisterAndRetrieveDescription()
    {
        $this->assertSame('', $this->fixture->getDescription());
        $description = 'description';

        $this->fixture->setDescription($description);

        $this->assertSame($description, $this->fixture->getDescription());
    }

    /**
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::getExcludePatterns
     * @covers phpDocumentor\Descriptor\Validator\Ruleset::setExcludePatterns
     */
    public function testRegisterAndRetrieveExcludePatterns()
    {
        $this->assertSame(array(), $this->fixture->getExcludePatterns());
        $excludePatterns = array('excludePatterns');

        $this->fixture->setExcludePatterns($excludePatterns);

        $this->assertSame($excludePatterns, $this->fixture->getExcludePatterns());
    }
}
