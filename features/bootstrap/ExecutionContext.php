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
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;

/**
 * Context class for the phpDocumentor Features.
 */
class ExecutionContext extends BehatContext
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
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return int
     */
    public function getReturnCode()
    {
        return $this->return_code;
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
    public function iRunPhpDocumentorWith(PyStringNode $code, $extraParameters = '')
    {
        $file = tempnam(sys_get_temp_dir(), 'pdb');
        file_put_contents($file, $code);
        $this->iRun("php bin/phpdoc.php -f $file -t build --config=none --force $extraParameters");
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
     * @When /^I run phpDocumentor with the "([^"]*)" template$/
     */
    public function iRunPhpdocumentorWithTheTemplate($arg1)
    {
        throw new PendingException();
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
        if ($this->getOutput() != trim($string->getRaw())) {
            throw new \Exception(
                "Actual output is:\n" . $this->getOutput()
            );
        }
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
                .$this->getOutput()
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
        if ($this->getOutput() != "") {
            throw new \Exception('Output has been detected: '.$this->getOutput());
        }
    }

    /**
     * @Given /^the configuration file has a transformation with the "([^"]*)" writer having "([^"]*)" as artifact$/
     */
    public function theConfigurationFileHasATransformationWithTheWriterHavingAsArtifact($arg1, $arg2)
    {
        throw new PendingException();
    }
}
