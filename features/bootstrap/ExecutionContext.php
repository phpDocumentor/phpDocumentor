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
    public function __construct()
    {
        if (file_exists($this->getTempXmlConfiguration())) {
            unlink($this->getTempXmlConfiguration());
        }
        $this->getCustomConfigurationAsXml(); // create new file
    }

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
        $tmp = $this->getTmpFolder();
        $this->iRun("php bin/phpdoc.php -t $tmp --config='{$this->getTempXmlConfiguration()}' --force");
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
        $tmp = $this->getTmpFolder();
        $this->iRun(
            "php bin/phpdoc.php -f $file_path -t $tmp --config='{$this->getTempXmlConfiguration()}' --force"
        );
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
        $tmp = $this->getTmpFolder();
        $file = tempnam(sys_get_temp_dir(), 'pdb');
        file_put_contents($file, $code);
        $this->iRun(
            "php bin/phpdoc.php -f $file -t $tmp --config='{$this->getTempXmlConfiguration()}' --force $extraParameters"
        );
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
        $tmp = $this->getTmpFolder();
        $this->iRun(
            "php bin/phpdoc.php -f $file_path -t $tmp --config=--config='{$this->getTempXmlConfiguration()}' "
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
        $tmp = $this->getTmpFolder();
        $this->iRun(
            "php bin/phpdoc.php -d $folder_path -t $tmp --config=--config='{$this->getTempXmlConfiguration()}' --force"
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

        $config->save($this->getTempXmlConfiguration());
    }

    /**
     *
     * @return DOMDocument
     */
    protected function getCustomConfigurationAsXml()
    {
        $filename = $this->getTempXmlConfiguration();
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
    /**
     *
     *
     *
     * @return string
     */
    public function getTmpFolder()
    {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'behatTests';
        if (!file_exists($tmp)) {
            mkdir($tmp);
        }

        return $tmp;
    }

    /**
     *
     *
     *
     * @return string
     */
    protected function getTempXmlConfiguration()
    {
        return $this->getTmpFolder() . DIRECTORY_SEPARATOR . 'phpdoc.xml';
    }
}
