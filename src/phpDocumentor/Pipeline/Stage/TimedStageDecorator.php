<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

use function is_object;
use function sprintf;

final class TimedStageDecorator
{
    /** @var callable */
    private $decoratedStage;

    public function __construct(private readonly LoggerInterface $logger, callable $decoratedStage)
    {
        $this->decoratedStage = $decoratedStage;
    }

    /**
     * Starts a timer before entering the stage, and logs the expired time afterwards.
     *
     * Since we support any stage, we do not know what payload is received or returned; so both are mixed.
     *
     * @return mixed
     */
    public function __invoke(mixed $payload)
    {
        $stopwatch = new Stopwatch();
        $decoratedStage = $this->decoratedStage;
        $name = is_object($decoratedStage) ? $decoratedStage::class : 'DYNAMIC';

        $stopwatch->start($name);
        $this->logger->notice(sprintf('Starting stage: %s', $name));

        try {
            $result = $decoratedStage($payload);
        } finally {
            $event = $stopwatch->stop($name);
            $this->logger->notice(sprintf('Completed stage: %s in %d ms', $name, $event->getDuration()));
        }

        return $result;
    }
}
