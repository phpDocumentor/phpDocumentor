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

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a skeleton template.
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
        foreach ($this->getTemplateNames() as $template_name) {
            $output->writeln('* '.$template_name);
        }
        $output->writeln('');

        return 0;
    }

    /**
     * Returns a list of all template names.
     *
     * @return string[]
     */
    protected function getTemplateNames()
    {
        // TODO: this directory needs to come from the parameter set in the DIC in the ServiceProvider
        $template_dir = dirname(__FILE__) . '/../../../../data/templates';
        if (!file_exists($template_dir)) {
            //Vendored installation
            $template_dir = dirname(__FILE__) . '/../../../../../../templates';
        }

        /** @var \RecursiveDirectoryIterator $files */
        $files = new \DirectoryIterator($template_dir);

        $template_names = array();
        while ($files->valid()) {
            $name = $files->getBasename();

            // skip abstract files
            if (!$files->isDir() || in_array($name, array('.', '..'))) {
                $files->next();
                continue;
            }

            $template_names[] = $name;
            $files->next();
        }

        return $template_names;
    }
}
