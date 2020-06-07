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

namespace phpDocumentor\Console\Command\Project;

use phpDocumentor\Descriptor\ProjectDescriptor\WithCustomSettings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function sprintf;
use function var_export;

final class ListSettingsCommand extends Command
{
    /** @var iterable<WithCustomSettings> */
    private $servicesWithCustomSettings;

    /**
     * @param iterable<WithCustomSettings> $servicesWithCustomSettings
     */
    public function __construct(iterable $servicesWithCustomSettings)
    {
        parent::__construct('settings:list');
        $this->servicesWithCustomSettings = $servicesWithCustomSettings;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('The following settings are supported using <info>--setting</info> or <info>-s</info>.');
        $output->writeln('');
        $output->writeln('<comment>Settings:</comment>');

        foreach ($this->servicesWithCustomSettings as $servicesWithCustomSetting) {
            foreach ($servicesWithCustomSetting->getDefaultSettings() as $setting => $default) {
                $output->writeln(
                    sprintf('  <info>%s</info> <comment>[default: %s]</comment>', $setting, var_export($default, true))
                );
            }
        }

        $output->writeln('');

        return 0;
    }
}
