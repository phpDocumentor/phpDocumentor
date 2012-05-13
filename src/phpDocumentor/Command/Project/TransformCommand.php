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
namespace phpDocumentor\Command\Project;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Transforms the structure file into the specified output format
 *
 * This task will execute the transformation rules described in the given
 * template (defaults to 'responsive') with the given source (defaults to
 * output/structure.xml) and writes these to the target location (defaults to
 * 'output').
 *
 * It is possible for the user to receive additional information using the
 * verbose option or stop additional information using the quiet option. Please
 * take note that the quiet option also disables logging to file.
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class TransformCommand extends ParseCommand
{
    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:transform')
            ->setAliases(array('transform'))
            ->setDescription(
                'Converts the PHPDocumentor structure file to documentation'
            )
            ->setHelp(
<<<TEXT
This task will execute the transformation rules described in the given
template (defaults to 'responsive') with the given source (defaults to
output/structure.xml) and writes these to the target location (defaults to
'output').

It is possible for the user to receive additional information using the
verbose option or stop additional information using the quiet option. Please
take note that the quiet option also disables logging to file.
TEXT
            );

        $this->addOption(
            'source', 's', InputOption::VALUE_OPTIONAL,
            'Path where the XML source file is located (optional)',
            'output/structure.xml'
        );
        $this->addOption(
            'target', 't', InputOption::VALUE_OPTIONAL,
            'Path where to store the generated output (optional)',
            'output'
        );
        $this->addOption(
            'template', null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Name of the template to use (optional)',
            array('responsive')
        );
        $this->addOption(
            'parseprivate', null, InputOption::VALUE_NONE,
            'Whether to parse DocBlocks marked with @internal tag'
        );
        $this->addOption(
            'progressbar', 'p', InputOption::VALUE_NONE,
            'Whether to show a progress bar; will automatically quiet logging '
            . 'to stdout'
        );
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('progressbar')) {
            \phpDocumentor_Transformer_Abstract::$event_dispatcher->connect(
                'transformer.writer.xsl.pre', array($this, 'echoProgress')
            );
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $source = $input->getOption('source');
        if (file_exists($source) and is_dir($source)) {
            $source .= DIRECTORY_SEPARATOR . 'structure.xml';
        }

        // initialize transformer
        $transformer = new \phpDocumentor_Transformer();
        $transformer->setTemplatesPath(
            \phpDocumentor_Core_Abstract::config()->paths->templates
        );
        $transformer->setTarget($input->getOption('target'));

        $transformer->setSource(
            $this->getTarget(
                $this->getOption($input, 'source', 'parser/target')
            )
        );

        $transformer->setTemplates($input->getOption('template'));
        $transformer->setParseprivate($input->getOption('parseprivate'));

        // add links to external docs
        $external_class_documentation = \phpDocumentor_Core_Abstract::config()
            ->getArrayFromPath('transformer/external-class-documentation');

        $external_class_documentation = (!is_numeric(
            current(array_keys($external_class_documentation))
        ))
            ? array($external_class_documentation)
            : $external_class_documentation;

        /** @var \phpDocumentor_Core_Config $doc */
        foreach ($external_class_documentation as $doc) {
            if (empty($doc)) {
                continue;
            }

            $transformer->setExternalClassDoc(
                (string)$doc['prefix'],
                (string)$doc['uri']
            );
        }

        $transformer->execute();

        return 0;
    }
}
