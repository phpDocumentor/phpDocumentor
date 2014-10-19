<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace Cilex;

/**
 * Describes a Service Provider that contains Commands.
 *
 * Because commands consume services when they are instantiated they should be registered after all services are
 * registered with the container. By applying this interface and registering all commands inside the
 * {@see self::registerCommands()} method Cilex will first register the services and after all services are registered
 * register the commands.
 */
interface CommandProviderInterface
{
    /**
     * Registers commands with the Container.
     *
     * @param Application $app
     *
     * @return void
     */
    public function registerCommands($app);
} 