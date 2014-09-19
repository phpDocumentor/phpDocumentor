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

namespace phpDocumentor\Descriptor\Validator\Ruleset;

use phpDocumentor\Descriptor\Validator\Rule;
use phpDocumentor\Descriptor\Validator\Ruleset;

/**
 * phpDocumentors default Ruleset where is defined that any issue known to phpDocumentor should be reported.
 */
class DefaultRuleset extends Ruleset
{
    public function __construct()
    {
        parent::__construct('Default');

        $this->addRule(new Rule('File.Summary.Missing', 'PPC:ERR-50000'));
        $this->addRule(new Rule('File.Package.CheckForDuplicate', 'PPC:ERR-50001'));
        $this->addRule(new Rule('File.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'));
        $this->addRule(new Rule('File.Subpackage.CheckForPackage', 'PPC:ERR-50004'));

        $this->addRule(new Rule('Class.Summary.Missing', 'PPC:ERR-50005'));
        $this->addRule(new Rule('Class.Package.CheckForDuplicate', 'PPC:ERR-50001'));
        $this->addRule(new Rule('Class.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'));
        $this->addRule(new Rule('Class.Subpackage.CheckForPackage', 'PPC:ERR-50004'));

        $this->addRule(new Rule('Interface.Summary.Missing', 'PPC:ERR-50009'));
        $this->addRule(new Rule('Interface.Package.CheckForDuplicate', 'PPC:ERR-50001'));
        $this->addRule(new Rule('Interface.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'));
        $this->addRule(new Rule('Interface.Subpackage.CheckForPackage', 'PPC:ERR-50004'));

        $this->addRule(new Rule('Trait.Summary.Missing', 'PPC:ERR-50009'));
        $this->addRule(new Rule('Trait.Package.CheckForDuplicate', 'PPC:ERR-50001'));
        $this->addRule(new Rule('Trait.Subpackage.CheckForDuplicate', 'PPC:ERR-50002'));
        $this->addRule(new Rule('Trait.Subpackage.CheckForPackage', 'PPC:ERR-50004'));

        $this->addRule(new Rule('Function.Summary.Missing', 'PPC:ERR-50011'));
        $this->addRule(new Rule('Function.Return.NotAnIdeDefault', 'PPC:ERR-50017'));
        $this->addRule(new Rule('Function.Param.NotAnIdeDefault', 'PPC:ERR-50018'));
        $this->addRule(new Rule('Function.Param.ArgumentInDocBlock', 'PPC:ERR-50015'));

        $this->addRule(new Rule('Method.Summary.Missing', 'PPC:ERR-50011'));
        $this->addRule(new Rule('Method.Return.NotAnIdeDefault', 'PPC:ERR-50017'));
        $this->addRule(new Rule('Method.Param.NotAnIdeDefault', 'PPC:ERR-50018'));
        $this->addRule(new Rule('Method.Param.ArgumentInDocBlock', 'PPC:ERR-50015'));

        $this->addRule(new Rule('Property.Summary.Missing', 'PPC:ERR-50007'));
    }
}
