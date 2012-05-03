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
class InstallCommand extends \Cilex\Command\Command
{
    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('template:install')
            ->setDescription(
                'Installs a template into phpDocumentor'
            )
            ->setHelp(
<<<HELP
Installs a template from the phpDocumentor template repository
(http://templates.phpdoc.org).

The first argument is the name of the template to install.

It may be necessary for this task to be executed with `sudo` if the template's
path is not writable by the current user. Please note that if phpDocumentor is
not installed using PEAR you need to provide the version number.
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

        return 0;
    }
}
