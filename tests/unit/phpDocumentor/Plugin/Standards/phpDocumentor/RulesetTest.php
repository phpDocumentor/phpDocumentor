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

namespace phpDocumentor\Plugin\Standards\phpDocumentor;

use Mockery as m;

/**
 * Tests to verify if the phpDocumentor Documentation Standard provides everything that is necessary.
 */
class RulesetTest extends \PHPUnit_Framework_TestCase
{
    /** @var Ruleset */
    private $fixture;

    /**
     * Initializes the fixture.
     */
    protected function setUp()
    {
        $this->fixture = new Ruleset();
    }

    /**
     * @covers phpDocumentor\Plugin\Standards\phpDocumentor\Ruleset::__construct
     */
    public function testNameIsSet()
    {
        $this->assertSame('phpDocumentor', $this->fixture->getName());
    }
    /**
     * @covers phpDocumentor\Plugin\Standards\phpDocumentor\Ruleset::__construct
     * @dataProvider provideRuleNamesAndMessages
     */
    public function testRuleIsSet($name, $message)
    {
        $rule = $this->fixture->getRule($name);
        $this->assertInstanceOf('phpDocumentor\Plugin\Standards\Rule', $rule);
        $this->assertSame($name, $rule->getRef());
        $this->assertSame($message, $rule->getMessage());
    }

    /**
     * Returns an array containing all names and messages in the phpDocumentor Standard.
     *
     * @return string[][]
     */
    public function provideRuleNamesAndMessages()
    {
        return array(
            array('File.Summary.Missing', 'PPC:ERR-50000'),
            array('File.Package.CheckForDuplicate', 'PPC:ERR-50001'),
            array('File.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'),
            array('File.Subpackage.CheckForPackage', 'PPC:ERR-50004'),
            array('Class.Summary.Missing', 'PPC:ERR-50005'),
            array('Class.Package.CheckForDuplicate', 'PPC:ERR-50001'),
            array('Class.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'),
            array('Class.Subpackage.CheckForPackage', 'PPC:ERR-50004'),
            array('Interface.Summary.Missing', 'PPC:ERR-50009'),
            array('Interface.Package.CheckForDuplicate', 'PPC:ERR-50001'),
            array('Interface.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'),
            array('Interface.Subpackage.CheckForPackage', 'PPC:ERR-50004'),
            array('Trait.Summary.Missing', 'PPC:ERR-50009'),
            array('Trait.Package.CheckForDuplicate', 'PPC:ERR-50001'),
            array('Trait.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'),
            array('Trait.Subpackage.CheckForPackage', 'PPC:ERR-50004'),
            array('Function.Summary.Missing', 'PPC:ERR-50011'),
            array('Function.Return.NotAnIdeDefault', 'PPC:ERR-50017'),
            array('Function.Param.NotAnIdeDefault', 'PPC:ERR-50018'),
            array('Function.Param.ArgumentInDocBlock', 'PPC:ERR-50015'),
            array('Method.Summary.Missing', 'PPC:ERR-50011'),
            array('Method.Return.NotAnIdeDefault', 'PPC:ERR-50017'),
            array('Method.Param.NotAnIdeDefault', 'PPC:ERR-50018'),
            array('Method.Param.ArgumentInDocBlock', 'PPC:ERR-50015'),
            array('Property.Summary.Missing', 'PPC:ERR-50007')
        );
    }
}
