<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;

final class EmittingMiddleware implements Middleware
{
    /**
     * @return object
     */
    public function execute(Command $command, callable $next)
    {
        assert($command instanceof CreateCommand);

        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            Dispatcher::getInstance()->dispatch(
                'parser.file.pre',
                PreFileEvent::createInstance($this)->setFile($command->getFile()->path())
            );
        }

        return $next($command);
    }
}
