<?php

namespace phpDocumentor\Infrastructure\Reflection\Middleware;

use League\Event\Emitter;
use phpDocumentor\DomainModel\ApiFileParsed;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;

class LoggingMiddleware implements Middleware
{
    /**
     * @var Emitter
     */
    private $emitter;

    public function __construct(Emitter $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * Executes this middle ware class.
     *
     * @param $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        $result = $next($command);

        if ($command instanceof CreateCommand) {
            $this->emitter->emit(new ApiFileParsed($command->getFile()->path()));
        }

        return $result;
    }
}
