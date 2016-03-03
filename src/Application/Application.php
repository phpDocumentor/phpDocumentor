<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application;

use DI\ContainerBuilder;
use phpDocumentor\Application\Console\Input\ArgvInput;
use Symfony\Component\Console\Application as ConsoleApplication;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 */
final class Application
{
    /**
     * Initializes all components used by phpDocumentor.
     *
     * @param array $values
     */
    public function __construct(
        array $values = [],
        $dependencyInjectionDefinitions = [__DIR__ . '/ContainerDefinitions.php']
    ) {
        $this->disableGarbageCollection();
        $this->setTimezone();
        $this->removePhpMemoryLimit();

        $container = $this->createContainer($values, $dependencyInjectionDefinitions);
        $this->console = $container->get(ConsoleApplication::class);
    }

    /**
     * Run the application and if no command is provided, use project:run.
     *
     * @return integer The exit code for this application
     */
    public function run()
    {
        $this->console->setAutoExit(false);

        return $this->console->run(new ArgvInput());
    }

    /**
     * phpDocumentor creates large nested, and sometimes recursive, data structures; by disabling garbage collection
     * we lose some memory efficiency but gain performance.
     *
     * The trade-off between memory efficiency and performance was made because this is not part of a long running
     * process. Should the application ever be used as a daemon then this decision should be revisited.
     *
     * @return void
     */
    private function disableGarbageCollection()
    {
        gc_disable();
    }

    /**
     * If the timezone is not set anywhere, set it to UTC.
     *
     * This is done to prevent any warnings being outputted in relation to using
     * date/time functions. What is checked is php.ini, and if the PHP version
     * is prior to 5.4, the TZ environment variable.
     *
     * @link http://php.net/manual/en/function.date-default-timezone-get.php for more information how PHP determines the
     *     default timezone.
     *
     * @return void
     */
    private function setTimezone()
    {
        if (false == ini_get('date.timezone')
            || (version_compare(phpversion(), '5.4.0', '<') && false === getenv('TZ'))
        ) {
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Ensure that the memory limit of PHP doesn't get in the way by disabling it.
     *
     * @return void
     */
    private function removePhpMemoryLimit()
    {
        ini_set('memory_limit', -1);
    }

    /**
     * Creates the dependency injection container user by phpDocumentor and disables the
     * use of annotations in it.
     *
     * @param string[] $values
     * @param array $definitions
     *
     * @return \DI\Container
     */
    private function createContainer(array $values, array $definitions)
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($values);
        foreach ($definitions as $definition) {
            $builder->addDefinitions($definition);
        }
        $builder->useAnnotations(false);
        $phpDiContainer = $builder->build();

        return $phpDiContainer;
    }
}
