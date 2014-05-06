<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Command\Template;

use Cilex\Command\Command;
use phpDocumentor\Transformer\Template\Factory;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a skeleton template.
 */
class ListCommand extends Command
{
    /** @var Factory */
    private $factory;

    /**
     * Initializes this command with its dependencies.
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        parent::__construct('template:list');

        $this->factory = $factory;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('template:list')
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
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Available templates:');
        foreach ($this->factory->getAllNames() as $template_name) {
            $output->writeln('* '.$template_name);
        }
        $output->writeln('');
    }
}
