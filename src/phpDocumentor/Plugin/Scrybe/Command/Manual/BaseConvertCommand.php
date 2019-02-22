<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Command\Manual;

use phpDocumentor\Plugin\Scrybe\Converter\ConverterInterface;
use phpDocumentor\Plugin\Scrybe\Converter\Factory as ConverterFactory;
use phpDocumentor\Plugin\Scrybe\Converter\Format;
use phpDocumentor\Plugin\Scrybe\Template\Factory;
use phpDocumentor\Plugin\Scrybe\Template\TemplateInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract Command class containing the scaffolding for the subsequent converting commands.
 */
abstract class BaseConvertCommand extends Command
{
    /** @var ConverterFactory */
    protected $converterFactory;

    /** @var Factory */
    protected $templateFactory;

    /** @var string The string representation of the output format */
    protected $output_format = Format\Format::HTML;

    /**
     * Initializes this command with a template and converter factory.
     *
     * @param string $name
     */
    public function __construct($name, Factory $templateFactory, ConverterFactory $converterFactory)
    {
        parent::__construct($name);

        $this->templateFactory = $templateFactory;
        $this->converterFactory = $converterFactory;
    }

    /**
     * Configures the options and default help text.
     */
    protected function configure()
    {
        $this
            ->addOption(
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'target location for output',
                'build'
            )
            ->addOption(
                'input-format',
                'i',
                InputOption::VALUE_OPTIONAL,
                'which input format does the documentation sources have?',
                'rst'
            )
            ->addOption(
                'title',
                null,
                InputOption::VALUE_OPTIONAL,
                'The title of this document',
                'Scrybe'
            )
            ->addOption(
                'template',
                null,
                InputOption::VALUE_OPTIONAL,
                'which template should be used to generate the documentation?',
                'default'
            )
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'One or more files or directories to fetch files from'
            );

        $this->setHelp(
            <<<DESCRIPTION
Generates reference documentation as {$this->output_format}.

You can define the type of files use as input using the <info>--input-format</info>
of <info>-i</info> option.

DESCRIPTION
        );
    }

    /**
     * Execute the transformation process to an output format as defined in the
     * $output_format class variable.
     *
     * @see BaseConvertCommand::$output_format to determine the output format.
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getHelper('phpdocumentor_logger')->connectOutputToLogging($output, $this);

        $converter = $this->getConverter($input);
        $converter->setOption('title', $input->getOption('title'));

        $output->writeln('Collect all documents');
        $files = $this->buildCollection(
            $input->getArgument('source'),
            $converter->getDefinition()->getInputFormat()->getExtensions()
        );

        $output->writeln('Converting documents');
        $files = $converter->convert($files, $this->getTemplate($input));

        $output->writeln('Writing converted documents to disk');
        $this->writeToDisk($files, $input->getOption('target'));

        $output->writeln('Writing assets to disk');
        $converter->getAssets()->copyTo($input->getOption('target'));
    }

    /**
     * @param string[] $files
     * @param string $destination
     */
    protected function writeToDisk($files, $destination)
    {
        foreach ($files as $relative_path => $contents) {
            $full_path = $destination . '/' . $relative_path;

            $destination_folder = dirname($full_path);
            if (!file_exists($destination_folder)) {
                mkdir($destination_folder, 0777, true);
            }

            file_put_contents($full_path, $contents);
        }
    }

    /**
     * Returns a template object based off the human-readable template name.
     *
     * @return TemplateInterface
     */
    protected function getTemplate(InputInterface $input)
    {
        $template = $this->getTemplateFactory()->get('twig');
        $template->setName($input->getOption('template'));

        return $template;
    }

    /**
     * Returns the converter for this operation.
     *
     * @return ConverterInterface
     */
    protected function getConverter(InputInterface $input)
    {
        return $this->getConverterFactory()->get($input->getOption('input-format'), $this->output_format);
    }

    /**
     * Constructs a Fileset collection and returns that.
     *
     * TODO: implement this
     *
     * @param array $sources    List of source paths.
     * @param array $extensions List of extensions to scan for in directories.
     *
     * @return Collection
     */
    protected function buildCollection(array $sources, array $extensions)
    {
//        $collection = new Collection();
//        $collection->setAllowedExtensions($extensions);
//        foreach ($sources as $path) {
//            if (is_dir($path)) {
//                $collection->addDirectory($path);
//                continue;
//            }
//
//            $collection->addFile($path);
//        }
//
//        return $collection;
    }

    /**
     * Returns a factory object that can return any Scrybe template.
     *
     * @return Factory
     */
    protected function getTemplateFactory()
    {
        return $this->templateFactory;
    }

    /**
     * Returns the factory for converters.
     *
     * @return ConverterFactory
     */
    public function getConverterFactory()
    {
        return $this->converterFactory;
    }
}
