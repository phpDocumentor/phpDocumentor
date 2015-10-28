<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Cli\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lists all templates known to phpDocumentor.
 */
class ListCommand extends Command
{
    /**
     * Initializes this command with its dependencies.
     */
    public function __construct()
    {
        parent::__construct('template:list');
    }

    /**
     * Initializes this command and sets the name, description, options and arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription(
                'Displays a listing of all available templates in phpDocumentor'
            )
            ->setHelp(
                <<<HELP
This task outputs a list of templates as available in phpDocumentor.
Please mind that custom templates which are situated outside phpDocumentor are not
shown in this listing.
HELP
            );
    }

    /**
     * Retrieves all template names from the Template Factory and sends those to stdout.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Available templates:');
//        foreach ($this->factory->getAllNames() as $template_name) {
//            $output->writeln('* '.$template_name);
//        }
        // TODO: re-add this functionality using the new Factory
        $output->writeln('');
    }
}
