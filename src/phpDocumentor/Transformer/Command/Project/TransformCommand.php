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
namespace phpDocumentor\Transformer\Command\Project;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Cache\Storage\StorageInterface;
use phpDocumentor\Command\ConfigurableCommand;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
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
class TransformCommand extends ConfigurableCommand
{
    /** @var ProjectDescriptorBuilder $builder */
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
            'progressbar',
            'p',
            InputOption::VALUE_NONE,
            'Whether to show a progress bar; will automatically quiet logging to stdout'
        );

        parent::configure();
    }

    /**
     * @return ProjectDescriptorBuilder
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
     * Returns the Cache.
     *
     * @return StorageInterface
     */
    protected function getCache()
    {
        return $this->getContainer()->offsetGet('descriptor.cache');
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

        // initialize transformer
        $transformer = $this->getTransformer();

        $target = $this->getOption($input, 'target', 'transformer/target');
        if (!$this->isAbsolute($target)) {
            $target = getcwd().DIRECTORY_SEPARATOR.$target;
        }
        $transformer->setTarget($target);

        $source = realpath($this->getOption($input, 'source', 'parser/target'));
        if (!file_exists($source) || !is_dir($source)) {
            throw new \Exception('Invalid source location provided, a path to an existing folder was expected');
        }

        $this->getCache()->getOptions()->setCacheDir($source);

        $projectDescriptor = $this->getBuilder()->getProjectDescriptor();
        $mapper = new ProjectDescriptorMapper($this->getCache());
        $output->writeTimedLog('Load cache', array($mapper, 'populate'), array($projectDescriptor));

        foreach ($this->getTemplates($input) as $template) {
            $output->writeTimedLog(
                'Preparing template "'. $template .'"',
                array($transformer->getTemplates(), 'load'),
                array($template, $transformer)
            );
        }
        $output->writeTimedLog(
            'Preparing ' . count($transformer->getTemplates()->getTransformations()) . ' transformations',
            array($this, 'loadTransformations'),
            array($transformer)
        );

        if ($progress) {
            $progress->start($output, count($transformer->getTemplates()->getTransformations()));
        }

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $output->writeTimedLog($pass->getDescription(), array($pass, 'execute'), array($projectDescriptor));
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
            if (is_array($value)) {
                if (isset($value['name'])) {
                    $templates[] = $value['name'];
                } else {
                    foreach ($value as $template) {
                        if (is_array($template)) {
                            $templates[] = $template['name'];
                        }
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
     * Load custom defined transformations.
     *
     * @param Transformer $transformer
     *
     * @todo this is an ugly implementation done for speed of development, should be refactored
     *
     * @return void
     */
    public function loadTransformations(Transformer $transformer)
    {
        $received = array();
        $transformations = $this->getConfigValueFromPath('transformations/transformation');
        if (is_array($transformations)) {
            if (isset($transformations['writer'])) {
                $received[] = new Transformation(
                    isset($transformations['query']) ? $transformations['query'] : '',
                    $transformations['writer'],
                    isset($transformations['source']) ? $transformations['source'] : '',
                    isset($transformations['artifact']) ? $transformations['artifact'] : ''
                );
            } else {
                foreach ($transformations as $transformation) {
                    if (is_array($transformation)) {
                        $received[] = new Transformation(
                            isset($transformations['query']) ? $transformations['query'] : '',
                            $transformations['writer'],
                            isset($transformations['source']) ? $transformations['source'] : '',
                            isset($transformations['artifact']) ? $transformations['artifact'] : ''
                        );
                    }
                }
            }
        }

        if (!empty($received)) {
            $template = new Template('__');
            foreach ($received as $transformation) {
                $template[] = $transformation;
            }
            $transformer->getTemplates()->append($template);
        }
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
