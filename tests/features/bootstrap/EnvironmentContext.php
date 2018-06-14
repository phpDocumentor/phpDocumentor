<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Behat\Contexts;

use Behat\Behat\Context;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

final class EnvironmentContext implements Context\Context
{
    private $workingDir;

    /** @var Process */
    private $process;

    private $binaryPath;

    /**
     * @var null
     */
    private $pharPath;

    /**
     * EnvironmentContext constructor.
     * @param string $workingDir
     * @param null $pharPath
     */
    public function __construct($workingDir, $pharPath = null)
    {
        $this->workingDir = $workingDir;
        $this->pharPath = $pharPath;
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
        $this->binaryPath = $this->pharPath ? __DIR__ . '/../../../' . $this->pharPath : __DIR__ . '/../../../bin/phpdoc';
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
        foreach ($ri as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
    }

    /**
     * @Given /^A single file named "([^"]*)" based on "([^"]*)"$/
     */
    public function loadASingleFile($dest, $source)
    {
        Assert::fileExists(__DIR__ . '/../assets/singlefile/' . $source);
        copy(__DIR__ . '/../assets/singlefile/' . $source, $this->getWorkingDir() . DIRECTORY_SEPARATOR . $dest);
    }

    /**
     * @Given /^A project named "([^"]*)" based on "([^"]*)"$/
     */
    public function loadAProject($dest, $source)
    {
        $sourceDir = __DIR__ . '/../assets/projects/' . $source;
        Assert::directory($sourceDir);
        $destDir = $this->getWorkingDir() . DIRECTORY_SEPARATOR . $dest;

        if (!mkdir($destDir, 0755) && !is_dir($destDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $destDir));
        }

        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            if ($item->isDir()) {
                if (!mkdir($destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName()) &&
                    !is_dir($destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName())
                ) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName()));
                }
            } else {
                copy($item, $destDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @Given /^I ran "phpdoc(?: ((?:\"|[^"])*))?"$/
     * @When /^I run "phpdoc(?: ((?:\"|[^"])*))?"$/
     */
    public function iRun($argumentsString)
    {
        $argumentsString .= ' --template=xml';
        $argumentsString = strtr($argumentsString, ['\'' => '"']);
        if ($this->process->isStarted()) {
            $this->process->clearErrorOutput()->clearOutput();
        }
//      the app is always run in debug mode to catch debug information and collect the AST that is written to disk
        $this->process->setCommandLine(
            sprintf('%s %s %s', 'php', escapeshellarg($this->binaryPath), 'run ' . $argumentsString . ' -vvv')
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^the application must have run successfully$/
     * @throws \Exception when exit code of phpdoc was not 0.
     */
    public function theApplicationMustHaveRunSuccessfully()
    {
        if ($this->process->getExitCode() !== 0) {
            throw new \Exception($this->process->getErrorOutput());
        }
    }

    /**
     * @Then /^output contains "([^"]*)"$/
     * @throws \Exception
     */
    public function theOutputContains($regex)
    {
        if (!strpos($this->process->getErrorOutput(), $regex)) {
            throw new \Exception(
                sprintf('output doesn\'t match "%s"', $regex)
            );
        }
    }

    /**
     * @Then /^output doesn't contain "([^"]*)"$/
     * @throws \Exception
     */
    public function theOutputContainNot($regex)
    {
        if (strpos($this->process->getErrorOutput(), $regex)) {
            throw new \Exception(
                sprintf('output contains "%s", which was not expected', $regex)
            );
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

    /**
     * @Then /^(\d+) files should be parsed$/
     */
    public function filesShouldBeParsed($count)
    {
        \PHPUnit\Framework\Assert::assertSame((int) $count, substr_count($this->process->getErrorOutput(), 'Parsing'));
    }
}
