<?php

namespace phpDocumentor\Reflection\Middleware;

use League\Event\Emitter;
use phpDocumentor\ApiReference\FileParsed;
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
            $this->emitter->emit(new FileParsed($command->getFilePath()));
        }

        return $result;
    }
}
