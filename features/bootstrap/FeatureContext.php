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
use phpDocumentor\Descriptor\ProjectDescriptor;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Process\Process;

/**
 * Context class for the phpDocumentor Features.
 */
class FeatureContext extends BehatContext
{
    /** @var Process the process used to execute phpDocumentor */
    protected $process;

    /** @var string path to the phpdoc binary file, including phpdoc.php */
    protected $binaryPath;

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     *
     * @return void
     */
    public static function cleanTestFolders()
    {
        if (is_dir(self::getTmpFolder())) {
            self::clearDirectory(self::getTmpFolder());
        }
    }

    /**
     * @beforeScenario
     */
    public function beforeScenario()
    {
        $this->binaryPath = __DIR__ . '/../../bin/phpdoc.php';
        $this->process = new Process(null);
        $this->process->setWorkingDirectory($this->getTmpFolder());
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
        foreach (explode("\n", $this->getOutput()) as $line) {
            if (trim($line) == $string) {
                $found = true;
            }
        }

        if (!$found) {
            throw new \Exception(
                "Actual output is:\n" . $this->getOutput()
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
        foreach (explode("\n", $this->getOutput()) as $line) {
            if (trim($line) == $string) {
                $found = true;
            }
        }

        if ($found) {
            throw new \Exception(
                "Actual output is:\n" . $this->getOutput()
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
        foreach (explode("\n", $this->getOutput()) as $line) {
            if (strpos(trim($line), $string) !== false) {
                $found = true;
            }
        }

        if (!$found) {
            throw new \Exception(
                "Actual output is:\n" . $this->getOutput()
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
        foreach (explode("\n", $this->getOutput()) as $line) {
            if (strpos(trim($line), $string) !== false) {
                $found = true;
            }
        }

        if ($found) {
            throw new \Exception(
                "Actual output is:\n" . $this->getOutput()
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
     * @Given /^a file named "([^"]*)" with:$/
     */
    public function aFileNamedWith($arg1, PyStringNode $string)
    {
        $folder = self::getTmpFolder();
        $dirname = ltrim(dirname($arg1), './');
        if (!file_exists($folder . $dirname)) {
            mkdir($folder . $dirname, 0777, true);
        }

        file_put_contents($folder . '/' . $arg1, $string);
    }

    /**
     * @When /^I run "phpdoc(?: ((?:\"|[^"])*))?"$/
     *
     * @param string $argumentsString
     */
    public function iRunPhpdoc($argumentsString = '')
    {
        $argumentsString = strtr($argumentsString, array('\'' => '"'));

        // the app is always run in debug mode to catch debug information and collect the AST that is written to disk
        $this->process->setCommandLine(
            sprintf('%s %s %s', 'php', escapeshellarg($this->binaryPath), $argumentsString . ' -vvv')
        );
        $this->process->start();
        $this->process->wait();
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
        $tmp = self::getTmpFolder();
        $this->iRunPhpdoc("-t $tmp --config='{$this->getTempXmlConfigurationPath()}' --force");
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
        $tmp = self::getTmpFolder();
        $this->iRunPhpdoc("-f $file_path -t $tmp --config='{$this->getTempXmlConfigurationPath()}' --force");
    }

    /**
     * Parses the given PHP code with phpDocumentor.
     *
     * @param PyStringNode $code
     *
     * @When /^I run phpDocumentor with:$/
     *
     * @return void
     */
    public function iRunPhpDocumentorWith(PyStringNode $code, $extraParameters = '')
    {
        $tmp = self::getTmpFolder();
        $file = tempnam($tmp, 'pdb');
        file_put_contents($file, $code);
        $this->iRunPhpdoc("-f $file -t $tmp --config='{$this->getTempXmlConfigurationPath()}' --force $extraParameters");
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
        $tmp = self::getTmpFolder();
        $this->iRunPhpdoc(
            "-f $file_path -t $tmp --config=--config='{$this->getTempXmlConfigurationPath()}' --force $options"
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
        $tmp = self::getTmpFolder();
        $this->iRunPhpdoc(
            "-d $folder_path -t $tmp --config=--config='{$this->getTempXmlConfigurationPath()}' --force"
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
        if ($this->getReturnCode() != 0) {
            throw new \Exception(
                'Return code was ' . $this->getReturnCode() . ' with output '
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
        if ($this->getReturnCode() == 0) {
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

    /**
     * @Given /^I have removed all files with the "([^"]*)" extension$/
     */
    public function iHaveRemovedAllFilesWithTheExtension($arg1)
    {
        $iterator = new DirectoryIterator(getcwd());

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getExtension() === $arg1) {
                unlink($file->getRealPath());
            }
        }
    }

    /**
     * @Then /^there should be no files with the "([^"]*)" extension$/
     */
    public function thereShouldBeNoFilesWithTheExtension($arg1)
    {
        $iterator = new DirectoryIterator(getcwd());

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->getExtension() === $arg1) {
                throw new \Exception('Found a file with the extension ' . $arg1 . ': ' . $file->getFilename());
            }
        }
    }

    /**
     * @Given /^the "([^"]*)" section of the configuration has$/
     */
    public function theSectionOfTheConfigurationHas($arg1, PyStringNode $string)
    {
        $config = $this->getCustomConfigurationAsXml();
        $element = $config->documentElement;

        $el = $element->getElementsByTagName($arg1)->item(0);
        if (!$el) {
            $el = new DOMElement($arg1);
            $element->appendChild($el);
        }

        $f = $config->createDocumentFragment();
        $f->appendXML((string)$string);
        $el->appendChild($f);

        $config->save($this->getTempXmlConfigurationPath());
    }

    /**
     * @Given /^a source file containing validation errors$/
     */
    public function aSourceFileContainingValidationErrors()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should get a file "([^"]*)" containing checkstyle error definitions$/
     */
    public function iShouldGetAFileContainingCheckstyleErrorDefinitions($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^a source file containing validation warnings$/
     */
    public function aSourceFileContainingValidationWarnings()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should get a file "([^"]*)" containing checkstyle warning definitions$/
     */
    public function iShouldGetAFileContainingCheckstyleWarningDefinitions($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^a source file containing no errors$/
     */
    public function aSourceFileContainingNoErrors()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should get a file "([^"]*)" containing no definitions$/
     */
    public function iShouldGetAFileContainingNoDefinitions($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I expect the file "([^"]*)"$/
     */
    public function iExpectTheFile($arg1)
    {
        $filename = self::getTmpFolder() . DIRECTORY_SEPARATOR . $arg1;
        if (!file_exists($filename)) {
            throw new \Exception("File with filename '{$filename}' could not be found");
        }

    }

    /**
     * @Then /^the parent class should link to "([^"]*)"$/
     */
    public function theParentClassShouldLinkTo($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^the AST has an expression "([^"]*)" with value:$/
     */
    public function theAstHasAnExpressionWithValue($arg1, PyStringNode $string)
    {
        $expression = new ExpressionLanguage();
        $expressionResult = $expression->evaluate($arg1, array('project' => $this->getAst()));

        if ($expressionResult === null) {
            throw new Exception('Expression "' . $arg1 . '" does not match any content in the AST');
        }

        if ($expressionResult != (string)$string) {
            throw new Exception(var_export($expressionResult, true) . ' does not match \'' . $string . '\'');
        }
    }

    /**
     * @Then /^the AST has an expression "([^"]*)" with value: "([^"]*)"$/
     */
    public function theAstHasAnExpressionWithValue2($arg1, $arg2)
    {
        $string = new PyStringNode($arg2);
        $this->theAstHasAnExpressionWithValue($arg1, $string);
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->process->getOutput();
    }

    /**
     * @return int
     */
    public function getReturnCode()
    {
        return $this->process->getExitCode();
    }

    /**
     *
     * @return DOMDocument
     */
    protected function getCustomConfigurationAsXml()
    {
        $filename = $this->getTempXmlConfigurationPath();
        if (!file_exists($filename)) {
            file_put_contents($filename, <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<phpdocumentor>
</phpdocumentor>
XML
            );
        }

        $dom_sxe = dom_import_simplexml(simplexml_load_file($filename));
        $dom = new DOMDocument('1.0');
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);
        return $dom;
    }

    private static function getTmpFolder()
    {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behatTests';
        if (!file_exists($tmp)) {
            mkdir($tmp);
        }

        return $tmp;
    }

    protected function getTempXmlConfigurationPath()
    {
        return self::getTmpFolder() . DIRECTORY_SEPARATOR . 'phpdoc.xml';
    }

    /**
     * Recursively empties a directory inclusing subdirectories.
     *
     * @param string $path
     *
     * @see https://github.com/Behat/Behat/blob/3.0/features/bootstrap/FeatureContext.php#L280 for the original method.
     *
     * @return void
     */
    private static function clearDirectory($path)
    {
        $files = scandir($path);
        array_shift($files);
        array_shift($files);

        foreach ($files as $file) {
            $file = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($file)) {
                self::clearDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($path);
    }

    /**
     * @return ProjectDescriptor|null
     */
    protected function getAst()
    {
        return unserialize(file_get_contents(self::getTmpFolder() . '/ast.dump'));
    }

    /**
     * @Then /^the AST has a[n]? "([^"]*)" at expression "([^"]*)"$/
     */
    public function theAstHasAAtExpression($arg1, $arg2)
    {
        $expression = new ExpressionLanguage();
        $expressionResult = $expression->evaluate($arg2, array('project' => $this->getAst()));

        if ($expressionResult === null) {
            throw new Exception('Expression "' . $arg2 . '" does not match any content in the AST');
        }

        $descriptorClass = '\\phpDocumentor\\Descriptor\\' . ucfirst($arg1) . 'Descriptor';
        if (!$expressionResult instanceof $descriptorClass) {
            throw new Exception('The value at the given expression is not a \'' . $arg1. '\'');
        }
    }
}
