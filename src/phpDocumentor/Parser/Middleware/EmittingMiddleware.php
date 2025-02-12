<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Webmozart\Assert\Assert;

final class EmittingMiddleware implements Middleware
{
    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    public function execute(Command $command, callable $next): object
    {
        Assert::isInstanceOf($command, CreateCommand::class);

        $this->eventDispatcher->dispatch(
            PreFileEvent::createInstance($this)->setFile($command->getFile()->path()),
            'parser.file.pre',
        );

        return $next($command);
    }
}
