<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command\Template;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a skeleton template.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ListCommand extends \Cilex\Command\Command
{
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

        /** @var \RecursiveDirectoryIterator $files */
        $files = new \DirectoryIterator(
            dirname(__FILE__).'/../../../../data/templates'
        );
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, array('.', '..'))) {
                $files->next();
                continue;
            }

            $output->writeln('* '.$name);
            $files->next();
        }
        $output->writeln('');

        return 0;
    }

}
