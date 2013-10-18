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

/**
 * Context class for the verification of HTML output..
 */
class HtmlOutputContext extends BehatContext
{
    protected $filename;

    /**
     * @Then /^I expect the file "([^"]*)"$/
     */
    public function iExpectTheFile($arg1)
    {
        /** @var ExecutionContext $executionContext */
        $executionContext = $this->getMainContext()->getSubcontext('execution');

        $this->filename = $executionContext->getTmpFolder() . DIRECTORY_SEPARATOR . $arg1;
        if (!file_exists($this->filename)) {
            throw new \Exception("File with filename '{$this->filename}' could not be found");
        }

    }

    /**
     * @Then /^the parent class should link to "([^"]*)"$/
     */
    public function theParentClassShouldLinkTo($arg1)
    {
        throw new PendingException();
    }
}
