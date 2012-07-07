<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $output;
    private $return_code;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^I am in the phpDocumentor root directory$/
     */
    public function iAmInThePhpdocumentorRootDirectory()
    {
        chdir(__DIR__.'/../..');
    }

    /**
     * @When /^I run "([^"]*)"$/
     */
    public function iRun($arg1)
    {
        if (file_exists('build/structure.xml')) {
            unlink('build/structure.xml');
        }
        exec($arg1.' 2>&1', $this->output, $this->return_code);
        $this->output = implode("\n", $this->output);
    }

    /**
     * @When /^I run phpDocumentor against no files or directories$/
     */
    public function iRunPhpdocumentorAgainstNoFilesOrDirectories()
    {
        $this->iRun("php bin/phpdoc.php -t build --config=none --force");
    }

    /**
     * @When /^I run phpDocumentor against the file "([^"]*)"$/
     */
    public function iRunPhpDocumentorAgainstTheFile($arg1)
    {
        $this->iRun("php bin/phpdoc.php -f $arg1 -t build --config=none --force");
    }

    /**
     * @When /^I run phpDocumentor with:$/
     */
    public function iRunPhpDocumentorWith(PyStringNode $arg1)
    {
        $file = tempnam(sys_get_temp_dir(), 'pdb');
        file_put_contents($file, $arg1);
        $this->iRun("php bin/phpdoc.php -f $file -t build --config=none --force");
        unlink($file);
    }

    /**
     * @When /^I run phpDocumentor against the file "([^"]*)" using option "([^"]*)"$/
     */
    public function iRunPhpDocumentorAgainstTheFileUsingOption($arg1, $arg2)
    {
        $this->iRun("php bin/phpdoc.php -f $arg1 -t build --config=none --force $arg2");
    }

    /**
     * @When /^I run phpDocumentor against the directory "([^"]*)"$/
     */
    public function iRunPhpDocumentorAgainstTheDirectory($arg1)
    {
        $this->iRun("php bin/phpdoc.php -d $arg1 -t build --config=none --force");
    }

    /**
     * @Then /^I should get:$/
     */
    public function iShouldGet(PyStringNode $string)
    {
        if ($this->output != trim($string->getRaw())) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^I should get a log entry "([^"]*)"$/
     */
    public function iShouldGetALogEntry($string)
    {
        $found = false;
        foreach (explode("\n", $this->output) as $line) {
            if (trim($line) == $string) {
                $found = true;
            }
        }

        if (!$found) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^I should not get a log entry "([^"]*)"$/
     */
    public function iShouldNotGetALogEntry($string)
    {
        $found = false;
        foreach (explode("\n", $this->output) as $line) {
            if (trim($line) == $string) {
                $found = true;
            }
        }

        if ($found) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^I should not get a log entry containing "([^"]*)"$/
     */
    public function iShouldNotGetALogEntryContaining($string)
    {
        $found = false;
        foreach (explode("\n", $this->output) as $line) {
            if (strpos(trim($line), $string) !== false) {
                $found = true;
            }
        }

        if ($found) {
            throw new \Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @Then /^my AST should contain the file "([^"]*)"$/
     */
    public function myAstShouldContainTheFile($arg1)
    {
        /** @var \SimpleXMLElement $structure  */
        $structure = simplexml_load_file('build/structure.xml');
        if (!$structure->xpath('/project/file[@path="'.$arg1.'"]')) {
            throw new \Exception(
                "File not found in structure file:\n" . $structure->asXML()
            );
        }
    }

    /**
     * @Then /^my AST should contain (\d+) class definitions$/
     */
    public function myAstShouldContainClassDefinitions($arg1)
    {
        /** @var \SimpleXMLElement $structure  */
        $structure = simplexml_load_file('build/structure.xml');
        if (count($structure->xpath('//class')) !== (int)$arg1) {
            throw new \Exception(
                "Class count did not match in structure file:\n"
                . $structure->asXML()
            );
        }
    }

    /**
     * @Then /^I should get an exception "([^"]*)"$/
     */
    public function iShouldGetAnException($arg1)
    {
        $this->iShouldGetALogEntry('[Exception]');
        $this->iShouldGetALogEntry(
            'No parsable files were found, did you specify any using the -f or '
            .'-d parameter?'
        );
    }

    /**
     * @Then /^the exit code should be zero$/
     */
    public function theExitCodeShouldBeZero()
    {
        if ($this->return_code != 0) {
            throw new \Exception(
                'Return code was '.$this->return_code.' with output '
                .$this->output
            );
        }
    }

    /**
     * @Then /^the exit code should be non-zero$/
     */
    public function theExitCodeShouldBeNonZero()
    {
        if ($this->return_code == 0) {
            throw new \Exception('Return code was 0');
        }
    }

    /**
     * @Then /^there should be no output$/
     */
    public function thereShouldBeNoOutput()
    {
        if ($this->output != "") {
            throw new \Exception('Output has been detected: '.$this->output);
        }
    }
}
