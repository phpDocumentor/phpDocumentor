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
class GenerateCommand extends \Cilex\Command\Command
{
    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('template:generate')
            ->setDescription(
                'Generates a skeleton template'
            )
            ->addOption(
                'target', 't',
                InputOption::VALUE_REQUIRED,
                'Target location where to generate the new template'
            )
            ->addOption(
                'name', null, InputOption::VALUE_REQUIRED,
                'The name for the new template'
            )
            ->addOption(
                'author', 'a', InputOption::VALUE_OPTIONAL,
                'Name of the author'
            )
            ->addOption(
                'given-version', null, InputOption::VALUE_OPTIONAL,
                'Version number of this template'
            )
            ->addOption(
                'force', null, InputOption::VALUE_NONE,
                'Forces generation of the new template, even if there '
                . 'is an existing template at that location'
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
        $name    = $input->getOption('name');
        $target  = $input->getOption('target');
        $author  = $input->getOption('author');
        $force   = $input->getOption('force');
        $version = $this->getVersion($input);

        $this->validateTargetOption($target);
        $this->validateNameOption($name);
        $path = $this->getDestinationLocation($target, $name);

        $output->writeln('Generating directory structure');
        $this->prepareLocation($path, $force);

        $output->writeln('Generating files');
        $this->generateTemplateCssFile($this->getDestinationLocationForCss($path));
        $this->generateIndexTemplate($path);
        $this->generateConfigurationFile($name, $version, $author, $path);

        $output->writeln('Finished generating a new template at: ' . $path);
        $output->writeln('');

        return 0;
    }

    protected function generateConfigurationFile($name, $version, $author, $path)
    {
        $template = preg_replace(
            array(
                '/\{\{\s*name\s*\}\}/',
                '/\{\{\s*version\s*\}\}/',
                '/\{\{\s*author\s*\}\}/'
            ),
            array($name, $version, $author),
            file_get_contents(
                dirname(__FILE__) . '/../../../../data/base_template/template.xml'
            )
        );

        file_put_contents($path . DIRECTORY_SEPARATOR . 'template.xml', $template);
    }

    protected function generateTemplateCssFile($css_path)
    {
        copy(
            dirname(__FILE__) . '/../../../../data/base_template/css/template.css',
            $css_path . DIRECTORY_SEPARATOR . 'template.css'
        );
    }

    protected function generateIndexTemplate($path)
    {
        copy(
            dirname(__FILE__) . '/../../../../data/base_template/index.xsl',
            $path . DIRECTORY_SEPARATOR . 'index.xsl'
        );
    }

    protected function getDestinationLocationForCss($path)
    {
        return $path . DIRECTORY_SEPARATOR . 'css';
    }

    protected function getVersion(InputInterface $input)
    {
        return $input->getOption('given-version')
            ? $input->getOption('given-version') : '1.0.0';
    }

    protected function prepareLocation($path, $remove_if_exists)
    { // if the template exists, check the force parameter and either throw an
        // exception of remove the existing folder.
        if (file_exists($path))
        {
            if (!$remove_if_exists)
            {
                throw new \InvalidArgumentException('The folder "' . $path . '" already exists');
            }
            else
            {
                echo 'Removing previous template' . PHP_EOL;
                `rm -rf $path`;
            }
        }

        $css_path = $this->getDestinationLocationForCss($path);
        mkdir($path);
        mkdir($css_path);
    }

    protected function getDestinationLocation($target, $name)
    {
        return $target . DIRECTORY_SEPARATOR . $name;
    }

    protected function validateNameOption($name)
    {
        if ($name == '')
        {
            throw new \InvalidArgumentException('No template name has been given');
        }
    }

    protected function validateTargetOption($target)
    { // do the sanity checks
        if (!file_exists($target) || !is_dir($target))
        {
            throw new \InvalidArgumentException('Target path "' . $target . '" must exist');
        }

        if (!is_writable($target))
        {
            throw new \InvalidArgumentException('Target path "' . $target . '" is not writable');
        }
    }
}
