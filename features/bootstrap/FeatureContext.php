<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Context class for the phpDocumentor Features.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class FeatureContext extends BehatContext
{
    /**
     * Contains the last output of a iRun command.
     *
     * @see iRun() for the location where the variable is filled
     *
     * @var string
     */
    private $output;

    /**
     * Contains the return code of the last iRun command.
     *
     * @see iRun() for the location where the variable is filled
     *
     * @var int
     */
    private $return_code;

    /**
     * Initializes context.
     *
     * Every scenario gets it's own context object.
     *
     * @param string[] $parameters context parameters (set them up through
     *    behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * Changes the current working directory to that of phpDocumentor's root.
     *
     * @Given /^I am in the phpDocumentor root directory$/
     *
     * @return void
     */
    public function iAmInThePhpdocumentorRootDirectory()
    {
        chdir(__DIR__.'/../..');
    }

    /**
     * Executes a command and sets the output and return code on this context.
     *
     * @param string $command The command to execute.
     *
     * @When /^I run "([^"]*)"$/
     *
     * @return void
     */
    public function iRun($command)
    {
        if (file_exists('build/structure.xml')) {
            unlink('build/structure.xml');
        }
        exec($command.' 2>&1', $this->output, $this->return_code);
        $this->output = implode("\n", $this->output);
    }

    /**
     * Execute a run of phpDocumentor without any files or folders.
     *
     * The configuration is explicitly disabled to prevent tainting via
     * the configuration.
     *
     * @When /^I run phpDocumentor against no files or directories$/
     *
     * @return void
     */
    public function iRunPhpdocumentorAgainstNoFilesOrDirectories()
    {
        $this->iRun("php bin/phpdoc.php -t build --config=none --force");
    }

    /**
     * Runs phpDocumentor with only the file that is provided in this command.
     *
     * The configuration is explicitly disabled to prevent tainting via
     * the configuration.
     *
     * @param string $file_path
     *
     * @When /^I run phpDocumentor against the file "([^"]*)"$/
     *
     * @return void
     */
    public function iRunPhpDocumentorAgainstTheFile($file_path)
    {
        $this->iRun(
            "php bin/phpdoc.php -f $file_path -t build --config=none --force"
        );
    }

    /**
     * Parses the given PHP code with phpDocumentor.
     *
     * The configuration is explicitly disabled to prevent tainting via
     * the configuration.
     *
     * @param PyStringNode $code
     *
     * @When /^I run phpDocumentor with:$/
     *
     * @return void
     */
    public function iRunPhpDocumentorWith(PyStringNode $code)
    {
        $file = tempnam(sys_get_temp_dir(), 'pdb');
        file_put_contents($file, $code);
        $this->iRun("php bin/phpdoc.php -f $file -t build --config=none --force");
        unlink($file);
    }

    /**
     * Executes phpDocumentor and provides additional options.
     *
     * @param string $file_path
     * @param string $options
     *
     * @When /^I run phpDocumentor against the file "([^"]*)" using option "([^"]*)"$/
     *
     * @return void
     */
    public function iRunPhpDocumentorAgainstTheFileUsingOption($file_path, $options)
    {
        $this->iRun(
            "php bin/phpdoc.php -f $file_path -t build --config=none "
            ."--force $options"
        );
    }

    /**
     * Executes phpDocumentor against the contents of a given folder.
     *
     * @param string $folder_path
     *
     * @When /^I run phpDocumentor against the directory "([^"]*)"$/
     *
     * @return void
     */
    public function iRunPhpDocumentorAgainstTheDirectory($folder_path)
    {
        $this->iRun(
            "php bin/phpdoc.php -d $folder_path -t build --config=none --force"
        );
    }

    /**
     * Verifies whether the output of an iRun When is equal to the given.
     *
     * @param PyStringNode $string
     *
     * @Then /^I should get:$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
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
     * Verifies whether one of the log entries is the same as the given.
     *
     * Please note that this method exactly checks the given except for leading
     * and trailing spaces and control characters; those are stripped first.
     *
     * @param string $string
     *
     * @Then /^I should get a log entry "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
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
     * Verifies whether a specific log entry was not thrown.
     *
     * @param string $string
     *
     * @Then /^I should not get a log entry "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
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
     * Verifies whether a log entry contains the given substring.
     *
     * @param string $string
     *
     * @Then /^I should get a log entry containing "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function iShouldGetALogEntryContaining($string)
    {
        $found = false;
        foreach (explode("\n", $this->output) as $line) {
            if (strpos(trim($line), $string) !== false) {
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
     * Verifies whether none of the log entries contain the given substring.
     *
     * @param string $string
     *
     * @Then /^I should not get a log entry containing "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
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
     * Verifies whether the AST contains a file element with the given path.
     *
     * @param string $file_path
     *
     * @Then /^my AST should contain the file "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function myAstShouldContainTheFile($file_path)
    {
        /** @var \SimpleXMLElement $structure  */
        $structure = simplexml_load_file('build/structure.xml');
        if (!$structure->xpath('/project/file[@path="'.$file_path.'"]')) {
            throw new \Exception(
                "File not found in structure file:\n" . $structure->asXML()
            );
        }
    }

    /**
     * Verifies whether the AST contains a file-level DocBlock with a non-empty
     * short description.
     *
     * @Then /^my AST should have a file level DocBlock with short description$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function myAstShouldHaveAFileLevelDocBlockWithShortDescription()
    {
        /** @var \SimpleXMLElement $structure  */
        $structure = simplexml_load_file('build/structure.xml');
        if (!$structure->xpath('/project/file/docblock[description != ""]')) {
            throw new \Exception(
                "File-level DocBlock not found in structure file:\n"
                . $structure->asXML()
            );
        }
    }

    /**
     * Verifies whether the AST contains the given number of class definitions.
     *
     * @param int $class_count
     *
     * @Then /^my AST should contain (\d+) class definitions$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function myAstShouldContainClassDefinitions($class_count)
    {
        /** @var \SimpleXMLElement $structure  */
        $structure = simplexml_load_file('build/structure.xml');
        if (count($structure->xpath('//class')) !== (int)$class_count) {
            throw new \Exception(
                "Class count did not match in structure file:\n"
                . $structure->asXML()
            );
        }
    }

    /**
     * Verifies whether an exception is thrown during excecution.
     *
     * @param string $exception_text
     *
     * @Then /^I should get an exception "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function iShouldGetAnException($exception_text)
    {
        $this->iShouldGetALogEntry('[Exception]');
        $this->iShouldGetALogEntry($exception_text);
    }

    /**
     * Verifies whether an exception is thrown during execution containing a
     * substring.
     *
     * @param string $exception_text
     *
     * @Then /^I should get an exception containing "([^"]*)"$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function iShouldGetAnExceptionContaining($exception_text)
    {
        $this->iShouldGetALogEntry('[Exception]');
        $this->iShouldGetALogEntryContaining($exception_text);
    }

    /**
     * Verifies whether the return code was 0 and thus execution was a success.
     *
     * @Then /^the exit code should be zero$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
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
     * Verifies whether the return code was not null and it was not a success.
     *
     * @Then /^the exit code should be non-zero$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function theExitCodeShouldBeNonZero()
    {
        if ($this->return_code == 0) {
            throw new \Exception('Return code was 0');
        }
    }

    /**
     * Verifies whether nothing was written to STDOUT.
     *
     * @Then /^there should be no output$/
     *
     * @throws \Exception if the condition is not fulfilled
     *
     * @return void
     */
    public function thereShouldBeNoOutput()
    {
        if ($this->output != "") {
            throw new \Exception('Output has been detected: '.$this->output);
        }
    }
}
