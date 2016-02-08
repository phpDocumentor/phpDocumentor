<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Tactician;

use Interop\Container\ContainerInterface;
use League\Tactician\Handler\Locator\HandlerLocator;
use Webmozart\Assert\Assert;

/**
 * Command Handler Locator for Tactician that can locate handlers based on a provided mapping or by suffixing the FQCN
 * of a Command with 'Handler' and determining if that is a valid class.
 */
final class ContainerLocator implements HandlerLocator
{
    /** @var ContainerInterface */
    private $container;

    /** @var string a mapping where the key is the FQCN of the Command and the value is the FQCN of the Handler */
    private $commandToHandlerMap = [];

    /**
     * Initializes a mapping between a Command and a Handler, if no mapping is provided then the Handler is found by
     * appending the suffix 'Handler' to the FQCN of the Command.
     *
     * @param ContainerInterface $container
     * @param string[]           $commandToHandlerMap
     */
    public function __construct(ContainerInterface $container, array $commandToHandlerMap = [])
    {
        $this->container           = $container;
        $this->commandToHandlerMap = $commandToHandlerMap;
    }

    /**
     * Retrieves the handler for a specified command class.
     *
     * @param string $commandName
     *
     * @throws \InvalidArgumentException if the provided command name does not match to an existing class.
     * @throws \InvalidArgumentException if the name of the command handler does not match to an existing class.
     *
     * @return object
     */
    public function getHandlerForCommand($commandName)
    {
        $handlerClassName = isset($this->commandToHandlerMap[$commandName])
            ? $this->commandToHandlerMap[$commandName]
            : $commandName . 'Handler';

        Assert::classExists($commandName);
        Assert::classExists($handlerClassName);

        return $this->container->get($handlerClassName);
    }
}
