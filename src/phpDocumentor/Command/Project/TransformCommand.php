<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
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
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Transformer\Transformer;

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
 */
class TransformCommand extends \phpDocumentor\Command\ConfigurableCommand
{
    /** @var BuilderAbstract $builder */
    protected $builder;

    /** @var Transformer $transformer */
    protected $transformer;

    /** @var Compiler $compiler */
    protected $compiler;

    public function __construct($builder, $transformer, $compiler)
    {
        parent::__construct('project:transform');

        $this->builder     = $builder;
        $this->transformer = $transformer;
        $this->compiler    = $compiler;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setAliases(array('transform'))
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
            'source',
            's',
            InputOption::VALUE_OPTIONAL,
            'Path where the XML source file is located (optional)'
        );
        $this->addOption(
            'target',
            't',
            InputOption::VALUE_OPTIONAL,
            'Path where to store the generated output (optional)'
        );
        $this->addOption(
            'template',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Name of the template to use (optional)'
        );
        $this->addOption(
            'parseprivate',
            null,
            InputOption::VALUE_NONE,
            'Whether to parse DocBlocks marked with @internal tag'
        );
        $this->addOption(
            'progressbar',
            'p',
            InputOption::VALUE_NONE,
            'Whether to show a progress bar; will automatically quiet logging to stdout'
        );

        parent::configure();
    }

    /**
     * @return \phpDocumentor\Descriptor\BuilderAbstract
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return \phpDocumentor\Transformer\Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // invoke parent to load custom config
        parent::execute($input, $output);

        /** @var \phpDocumentor\Console\Helper\ProgressHelper $progress  */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->connectOutputToLogging($output);
        }

        $output->writeln('Initializing transformer');

        // initialize transformer
        $transformer = $this->getTransformer();

        $target = $this->getOption($input, 'target', 'transformer/target');
        if (!$this->isAbsolute($target)) {
            $target = getcwd().DIRECTORY_SEPARATOR.$target;
        }
        $transformer->setTarget($target);

        $source = realpath($this->getOption($input, 'source', 'parser/target'));
        if (file_exists($source) && is_dir($source)) {
            $source .= DIRECTORY_SEPARATOR . 'structure.xml';
        }

        if ($source && is_readable($source)) {
            $this->getBuilder()->import(file_get_contents($source));
        }

        foreach ($this->getTemplates($input) as $template) {
            $output->writeln('Loading template "' . $template . '"');
            $transformer->getTemplates()->load($template, $transformer);
        }
        $output->writeln('Prepared ' . count($transformer->getTemplates()->getTransformations()) . ' transformations');

        $transformer->setParseprivate($input->getOption('parseprivate'));

        // add links to external docs
        $external_class_documentation = (array)$this->getConfigValueFromPath(
            'transformer/external-class-documentation'
        );
        if (!isset($external_class_documentation[0])) {
            $external_class_documentation = array($external_class_documentation);
        }

        foreach ($external_class_documentation as $doc) {
            if (empty($doc)) {
                continue;
            }

            $transformer->setExternalClassDoc((string)$doc['prefix'], (string)$doc['uri']);
        }

        if ($progress) {
            $progress->start($output, count($transformer->getTemplates()->getTransformations()));
        }

        $projectDescriptor = $this->getBuilder()->getProjectDescriptor();
        foreach ($this->compiler as $pass) {
            $timerStart = microtime(true);
            $pass->execute($projectDescriptor);
            $output->writeln(
                sprintf('Process compiler pass in %.4fs: \\%s', microtime(true) - $timerStart, get_class($pass))
            );
        }

        if ($progress) {
            $progress->finish();
        }

        return 0;
    }

    /**
     * Retrieves the templates to be used by analyzing the options and the configuration.
     *
     * @param \Symfony\Component\Console\Input\ArgvInput $input
     *
     * @return string[]
     */
    protected function getTemplates(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $templates = $input->getOption('template');
        if (!$templates) {
            $value = $this->getConfigValueFromPath('transformations/template');
            if ($value) {
                foreach ($value as $template) {
                    if (is_array($template)) {
                        $templates[] = $template['name'];
                    }
                }
            }
        }

        if (!$templates) {
            $templates = array('responsive');
        }

        return $templates;
    }

    /**
     * Adds the transformer.transformation.post event to advance the progressbar.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Symfony\Component\Console\Helper\HelperInterface|null
     */
    protected function getProgressBar(InputInterface $input)
    {
        $progress = parent::getProgressBar($input);
        if (!$progress) {
            return null;
        }

        $this->getService('event_dispatcher')->addListener(
            'transformer.transformation.post',
            function () use ($progress) {
                $progress->advance();
            }
        );

        return $progress;
    }
}
