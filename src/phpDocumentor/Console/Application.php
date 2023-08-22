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

namespace phpDocumentor\Console;

use Monolog\Handler\PsrHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function sprintf;
use function strlen;

class Application extends BaseApplication
{
    /** @param iterable<Command> $commands */
    public function __construct(iterable $commands, EventDispatcher $eventDispatcher, Logger $logger)
    {
        parent::__construct('phpDocumentor', \phpDocumentor\Application::VERSION());

        foreach ($commands as $command) {
            $this->add($command);
        }

        $this->setDefaultCommand('project:run');
        $this->setDispatcher($eventDispatcher);

        $eventDispatcher->addListener(
            ConsoleEvents::COMMAND,
            static function (ConsoleEvent $event) use ($logger): void {
                $logger->pushHandler(new PsrHandler(new ConsoleLogger($event->getOutput())));
            },
        );
    }

    protected function getCommandName(InputInterface $input): string|null
    {
        try {
            if ($this->looksLikeACommandName($input->getFirstArgument())) {
                $this->find($input->getFirstArgument());

                return $input->getFirstArgument();
            }
        } catch (CommandNotFoundException) {
            //Empty by purpose
        }

        // the regular setDefaultCommand option does not allow for options and arguments; with this workaround
        // we can have options and arguments when the first element in the argv options is not a recognized
        // command name.
        return 'project:run';
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Location of a custom configuration file',
            ),
        );
        $inputDefinition->addOption(
            new InputOption('log', null, InputOption::VALUE_OPTIONAL, 'Log file to write to'),
        );

        return $inputDefinition;
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion(): string
    {
        return sprintf('%s <info>v%s</info>', $this->getName(), $this->getVersion());
    }

    /**
     * Only interpret the first argument as a potential command name if it is set and less than 100 characters.
     *
     * Anything above 255 characters will cause PHP warnings; and 100  should never occur anyway as a single
     * command name.
     *
     * @link https://github.com/phpDocumentor/phpDocumentor/issues/3215
     */
    private function looksLikeACommandName(string|null $argument): bool
    {
        return $argument !== null && strlen($argument) < 100;
    }
}
