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
 * Context class for the phpDocumentor Features.
 */
class CheckstyleContext extends BehatContext
{
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
}
