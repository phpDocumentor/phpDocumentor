<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use function assert;
use function class_exists;

final class EmittingMiddleware implements Middleware
{
    public function execute(Command $command, callable $next) : object
    {
        assert($command instanceof CreateCommand);

        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            Dispatcher::getInstance()->dispatch(
                PreFileEvent::createInstance($this)->setFile($command->getFile()->path()),
                'parser.file.pre'
            );
        }

        return $next($command);
    }
}
