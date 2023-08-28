<?php

declare(strict_types=1);

namespace phpDocumentor\Console\Command\Project;

use phpDocumentor\Transformer\Template\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TemplateListCommand extends Command
{
    public function __construct(private Factory $templateFactory)
    {
        parent::__construct('templates:list');
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('The following templates are available:');
        $output->writeln('');
        $output->writeln('<comment>Templates:</comment>');

        foreach ($this->templateFactory->getAllNames() as $template) {
            $output->writeln('  <info>' . $template . '</info>');
        }

        $output->writeln('');

        return 0;
    }
}
