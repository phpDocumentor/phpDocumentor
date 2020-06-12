<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @link      https://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Behat\Contexts\Template;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use phpDocumentor\Behat\Contexts\EnvironmentContext;
use Symfony\Component\Process\Process;

final class TemplateContext implements Context
{
    /** @var EnvironmentContext */
    private $environmentContext;

    /** @var Process */
    private $webserver;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope) : void
    {
        $environment = $scope->getEnvironment();

        $this->environmentContext = $environment->getContext(EnvironmentContext::class);
    }

    /** @BeforeScenario */
    public function beforeScenario() : void
    {
        $workingDir = $this->environmentContext->getWorkingDir();

        $this->webserver = new Process(
                sprintf('php -S localhost:8081 -t %s', escapeshellarg($workingDir))
        );

        $this->webserver->start(function () {
            var_dump("Server started");
        });
       // sleep(1);
    }

    /**
     * @AfterScenario
     */
    public function cleanup() : void
    {
        if ($this->webserver->isStarted()) {
            $this->webserver->stop();
        }
    }
}
