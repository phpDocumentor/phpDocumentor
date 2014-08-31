<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Command\Manual;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use phpDocumentor\Plugin\Scrybe\Converter\Format;
use phpDocumentor\Plugin\Scrybe\Converter\ToLatexInterface;

/**
 * Command used to tell the application to convert from a format to Latex.
 */
class ToLatexCommand extends BaseConvertCommand
{
    /** @var string The string representation of the output format */
    protected $output_format = Format\Format::LATEX;

    /**
     * Defines the name, description and additional options for this command
     * and inherits the behaviour of the parent configure.
     *
     * @see ConvertCommandAbstract::configure() for the common business rules.
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('manual:to-latex');
        $this->setDescription(
            'Generates reference documentation as a single Latex file'
        );

        $this->addOption(
            'author',
            null,
            InputOption::VALUE_OPTIONAL,
            'Name of the author'
        );
        $this->addOption(
            'cover-logo',
            null,
            InputOption::VALUE_OPTIONAL,
            'Path to a cover logo relative to the source root'
        );
        $this->addOption(
            'toc',
            null,
            InputOption::VALUE_OPTIONAL,
            'Whether the document should have a table of contents',
            true
        );
    }

    /**
     * Returns and configures the converter for this operation.
     *
     * This method overrides the parent getConverter method and invokes the
     * configureConverterFromInputOptions() method to set the options coming
     * from the Input object.
     *
     * @param InputInterface $input
     *
     * @see BaseConvertCommand::getConverter() for the basic functionality.
     *
     * @return \phpDocumentor\Plugin\Scrybe\Converter\ConverterInterface
     */
    protected function getConverter(InputInterface $input)
    {
        /** @var ToLatexInterface $converter  */
        $converter = parent::getConverter($input);
        $this->configureConverterFromInputOptions($converter, $input);

        return $converter;
    }

    /**
     * Configures the converter with the options provided by the Input options.
     *
     * @param ToLatexInterface $converter
     * @param InputInterface   $input
     *
     * @throws \InvalidArgumentException if the provided converter is not derived
     *     from ToLatexInterface
     *
     * @return void
     */
    protected function configureConverterFromInputOptions($converter, $input)
    {
        if (!$converter instanceof ToLatexInterface) {
            throw new \InvalidArgumentException(
                'The converter used to process '
                . $input->getOption('input-format') . ' should implement the '
                . 'phpDocumentor\Plugin\Scrybe\Converter\ToPdfInterface'
            );
        }

        $converter->setTitle($input->getOption('title'));
        $converter->setAuthor($input->getOption('author'));
        $converter->setCoverLogo($input->getOption('cover-logo'));
        $converter->setTableOfContents($input->getOption('toc') !== 'false');
    }
}
