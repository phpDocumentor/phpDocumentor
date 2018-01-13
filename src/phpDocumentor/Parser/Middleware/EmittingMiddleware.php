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

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\Middleware\Middleware;

final class EmittingMiddleware implements Middleware
{
    /**
     * Executes this middle ware class.
     *
     * @param callable $next
     * @return object
     */
    public function execute($command, callable $next)
    {
        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            Dispatcher::getInstance()->dispatch(
                'parser.file.pre',
                PreFileEvent::createInstance($this)->setFile($command->getFile()->path())
            );
        }

        return $next($command);
    }
}
