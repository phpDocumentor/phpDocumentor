<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts;

use Behat\Behat\Context;
use Behat\Gherkin\Node\PyStringNode;
use FilesystemIterator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

final class EnvironmentContext implements Context\Context
{
    private $workingDir;

    /** @var  Process */
    private $process;


    private $binaryPath;

    /**
     * EnvironmentContext constructor.
     */
    public function __construct($workingDir)
    {
        $this->workingDir = $workingDir;
    }


    /**
     * @beforeScenario
     */
    public function beforeScenario()
    {
        if (!is_dir($this->getWorkingDir())) {
            mkdir($this->getWorkingDir(), 0755, true);
        }

        Assert::directory($this->getWorkingDir());
        $this->binaryPath = __DIR__ . '/../../../bin/phpdoc';
        $this->process = new Process(null);
        $this->process->setWorkingDirectory($this->getWorkingDir());
        chdir($this->getWorkingDir());
    }

    /**
     * @AfterScenario
     */
    public function cleanup()
    {
        $di = new RecursiveDirectoryIterator($this->getWorkingDir(), FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ( $ri as $file ) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
    }

    /**
     * @Given /^A single file named "([^"]*)" based on "([^"]*)"$/
     */
    public function loadASingleFile($dest, $source)
    {
        Assert::fileExists(__DIR__ . '/../assets/singlefile/'. $source);
        copy(__DIR__ . '/../assets/singlefile/'. $source, $this->getWorkingDir() . DIRECTORY_SEPARATOR . $dest);
    }

    /**
     * @When /^I run "phpdoc(?: ((?:\"|[^"])*))?"$/
     */
    public function iRun($argumentsString)
    {
        $argumentsString .= ' --template=xml';
        $argumentsString = strtr($argumentsString, array('\'' => '"'));
//die( sprintf('%s %s %s', 'php', escapeshellarg($this->binaryPath), $argumentsString . ' -vvv'));
//         the app is always run in debug mode to catch debug information and collect the AST that is written to disk
        $this->process->setCommandLine(
            sprintf('%s %s %s', 'php', escapeshellarg($this->binaryPath), $argumentsString . ' -vvv')
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^the application must have run successfully$/
     */
    public function theApplicationMustHaveRunSuccessfully()
    {
        if ($this->process->getExitCode() !== 0) {
            throw new \Exception($this->process->getErrorOutput());
        }
    }

    public function getWorkingDir()
    {
        return $this->workingDir;
    }

    public function getErrorOutput()
    {
        return $this->process->getErrorOutput();
    }
}
