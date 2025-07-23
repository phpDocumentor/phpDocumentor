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

use phpDocumentor\AutoloaderLocator;
use phpDocumentor\Extension\ExtensionHandler;
use phpDocumentor\Version;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function array_merge;
use function getcwd;
use function is_array;
use function sprintf;

class Application extends BaseApplication
{
    /** @param string[] $composerExtensionsDirs An array of directories where composer installeed extensions are located. */
    public function __construct(private array $composerExtensionsDirs = [])
    {
        parent::__construct('phpDocumentor', (new Version())->getVersion());
    }

    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $extensionsDirs = [];
        if ($input->hasParameterOption('--no-extensions') === false) {
            $extensionsDirs = $input->getParameterOption(
                '--extensions-dir',
                $this->getDefinition()->getOption('extensions-dir')->getDefault(),
            );

            if (! is_array($extensionsDirs)) {
                $extensionsDirs = [$extensionsDirs];
            }

            $extensionsDirs = array_merge($extensionsDirs, $this->composerExtensionsDirs);
        }

        $extensionHandler = ExtensionHandler::getInstance($extensionsDirs);
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create(
            AutoloaderLocator::findVendorPath(),
            $extensionHandler,
        );

        $commands = $container->findTaggedServiceIds('console.command');
        foreach ($commands as $id => $_command) {
            $this->add($container->get($id));
        }

        $this->setDefaultCommand('project:run', false);

        $eventDispatcher = $container->get(EventDispatcher::class);
        $logger = $container->get(LoggerInterface::class);
        $this->setDispatcher($eventDispatcher);

        $eventDispatcher->addListener(
            ConsoleEvents::COMMAND,
            static function (ConsoleEvent $event) use ($logger): void {
                $logger->pushHandler(new ConsoleLogHandler(new SymfonyStyle($event->getInput(), $event->getOutput())));
            },
        );

        $eventDispatcher->addListener(
            ConsoleEvents::COMMAND,
            [$extensionHandler, 'onBoot'],
        );

        return parent::doRun($input, $output);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        // We are replacing the default command argument with a custom one that allows for a default value.
        return new InputDefinition(
            $inputDefinition->getOptions() +
            [
                new InputArgument(
                    'command',
                    InputArgument::OPTIONAL,
                    'The command to execute',
                    'project:run',
                ),
                new InputOption(
                    'config',
                    'c',
                    InputOption::VALUE_OPTIONAL,
                    'Location of a custom configuration file',
                ),
                new InputOption(
                    'extensions-dir',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'extensions directory to load extensions from',
                    getcwd() . '/.phpdoc/extensions',
                ),
                new InputOption(
                    'no-extensions',
                    null,
                    InputOption::VALUE_NONE,
                    'Do not load any extensions',
                ),
                new InputOption('log', null, InputOption::VALUE_OPTIONAL, 'Log file to write to'),
            ],
        );
    }

    protected function getCommandName(InputInterface $input): string|null
    {
        if ($input->getFirstArgument() === null) {
            return 'run';
        }

        return $input->getArgument('command');
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
}
