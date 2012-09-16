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
namespace phpDocumentor\Command\Template;

use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates a skeleton template.
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

    /**
     * Generates a new configuration file in the given target path.
     *
     * @param string $name    Name of the template.
     * @param string $version Version number in the format x.y.z (numerics only)
     * @param string $author  Name of the author
     * @param string $path    Destination directory for this file.
     *
     * @return void
     */
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

    /**
     * Generates a new CSS file at the given location.
     *
     * @param string $css_path Directory where to generate CSS file.
     *
     * @return void
     */
    protected function generateTemplateCssFile($css_path)
    {
        copy(
            dirname(__FILE__) . '/../../../../data/base_template/css/template.css',
            $css_path . DIRECTORY_SEPARATOR . 'template.css'
        );
    }

    /**
     * Generate basic index template.
     *
     * @param string $path Directory where to generate index file.
     *
     * @return void
     */
    protected function generateIndexTemplate($path)
    {
        copy(
            dirname(__FILE__) . '/../../../../data/base_template/index.xsl',
            $path . DIRECTORY_SEPARATOR . 'index.xsl'
        );
    }

    /**
     * Returns the destination path for CSS files with the given base path.
     *
     * @param string $path Base path where the template should be generated.
     *
     * @return string
     */
    protected function getDestinationLocationForCss($path)
    {
        return $path . DIRECTORY_SEPARATOR . 'css';
    }

    /**
     * Returns the version number for this template.
     *
     * @param InputInterface $input contains having the given-version option.
     *
     * @return string
     */
    protected function getVersion(InputInterface $input)
    {
        return $input->getOption('given-version')
            ? $input->getOption('given-version')
            : '1.0.0';
    }

    /**
     * Prepares the target location
     *
     * @param string  $path             Directory where the base of the template
     *     is.
     *
     * @param boolean $remove_if_exists Deletes any pre-existing directory.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareLocation($path, $remove_if_exists)
    {
        // if the template exists, check the force parameter and either throw an
        // exception of remove the existing folder.
        if (file_exists($path)) {
            if (!$remove_if_exists) {
                throw new \InvalidArgumentException(
                    'The folder "' . $path . '" already exists'
                );
            } else {
                echo 'Removing previous template' . PHP_EOL;
                `rm -rf $path`;
            }
        }

        $css_path = $this->getDestinationLocationForCss($path);
        mkdir($path);
        mkdir($css_path);
    }

    /**
     * Returns the destination path.
     *
     * @param string $target Directory where to generate the template.
     * @param string $name   Name of the template.
     *
     * @return string
     */
    protected function getDestinationLocation($target, $name)
    {
        return $target . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Validates the name for this template.
     *
     * @param string $name The name for this template.
     *
     * @throws \InvalidArgumentException if no name has been provided.
     *
     * @return void
     */
    protected function validateNameOption($name)
    {
        if ($name == '') {
            throw new \InvalidArgumentException(
                'No template name has been given'
            );
        }
    }

    /**
     * Validates whether the target location exists and is writable.
     *
     * @param string $target The target directory where to store the template.
     *
     * @throws \InvalidArgumentException if the target directory does not exist.
     * @throws \InvalidArgumentException if the target directory is not writable.
     *
     * @return void
     */
    protected function validateTargetOption($target)
    {
        // do the sanity checks
        if (!file_exists($target) || !is_dir($target)) {
            throw new \InvalidArgumentException(
                'Target path "' . $target . '" must exist'
            );
        }

        if (!is_writable($target)) {
            throw new \InvalidArgumentException(
                'Target path "' . $target . '" is not writable'
            );
        }
    }
}
