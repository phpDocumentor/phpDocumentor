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

namespace phpDocumentor\Parser\Command\Project;

use phpDocumentor\Command\Command;
use phpDocumentor\Configuration;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Parser\Parser;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zend\I18n\Translator\Translator;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
final class ParseCommand extends Command
{
    /** @var Parser $parser */
    protected $parser;

    /** @var Translator */
    protected $translator;

    /** @var Finder */
    private $exampleFinder;

    public function __construct($parser, $translator, $exampleFinder)
    {
        $this->parser        = $parser;
        $this->translator    = $translator;
        $this->exampleFinder = $exampleFinder;

        parent::__construct('project:parse');
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        // minimization of the following expression
        $optionalArrayFlag = InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY;

        $this->setAliases(array('parse'))
            ->setDescription($this->__('PPCPP-DESCRIPTION'))
            ->setHelp($this->__('PPCPP-HELPTEXT'))
            ->addOption('filename', 'f', $optionalArrayFlag, $this->__('PPCPP:OPT-FILENAME'))
            ->addOption('directory', 'd', $optionalArrayFlag, $this->__('PPCPP:OPT-DIRECTORY'))
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TARGET'))
            ->addOption('encoding', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-ENCODING'))
            ->addOption('extensions', 'e', $optionalArrayFlag, $this->__('PPCPP:OPT-EXTENSIONS'))
            ->addOption('ignore', 'i', $optionalArrayFlag, $this->__('PPCPP:OPT-IGNORE'))
            ->addOption('ignore-tags', null, $optionalArrayFlag, $this->__('PPCPP:OPT-IGNORETAGS'))
            ->addOption('hidden', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-HIDDEN'))
            ->addOption('ignore-symlinks', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-IGNORESYMLINKS'))
            ->addOption('markers', 'm', $optionalArrayFlag, $this->__('PPCPP:OPT-MARKERS'))
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TITLE'))
            ->addOption('force', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-FORCE'))
            ->addOption('visibility', null, $optionalArrayFlag, $this->__('PPCPP:OPT-VISIBILITY'))
            ->addOption('sourcecode', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-SOURCECODE'))
            ->addOption('progressbar', 'p', InputOption::VALUE_NONE, $this->__('PPCPP:OPT-PROGRESSBAR'))
            ->addOption('parseprivate', null, InputOption::VALUE_NONE, 'PPCPP:OPT-PARSEPRIVATE')
            ->addOption(
                'defaultpackagename',
                null,
                InputOption::VALUE_OPTIONAL,
                $this->__('PPCPP:OPT-DEFAULTPACKAGENAME'),
                'Default'
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception if the target location is not a folder.
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->populateConfiguration($input);
        $this->parser->boot($configuration->getParser());

        $this->exampleFinder->setSourceDirectory($this->parser->getFiles()->getProjectRoot());
        $this->exampleFinder->setExampleDirectories($configuration->getFiles()->getExamples());

        $progress = $this->startProgressbar($input, $output, $this->parser->getFiles()->count());
        $projectDescriptor = $this->parser->parse($this->parser->getFiles());
        $projectDescriptor->setName($configuration->getTitle());
        $this->finishProgressbar($progress);

        $projectDescriptor->setPartials($this->getService('partials'));

        return 0;
    }

    private function populateConfiguration(InputInterface $input)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getService('config');
        if ($input->getOption('filename')) {
            $configuration->getFiles()->setFiles($input->getOption('filename'));
        }
        if ($input->getOption('directory')) {
            $configuration->getFiles()->setDirectories($input->getOption('directory'));
        }
        if ($input->getOption('target')) {
            $configuration->getParser()->setTarget($input->getOption('target'));
        }
        if ($input->getOption('encoding')) {
            $configuration->getParser()->setEncoding($input->getOption('encoding'));
        }
        if ($input->getOption('extensions')) {
            $configuration->getParser()->setExtensions($input->getOption('extensions'));
        }
        if ($input->getOption('ignore')) {
            $configuration->getFiles()->setIgnore($input->getOption('ignore'));
        }
        if ($input->getOption('hidden')) {
            $configuration->getFiles()->setIgnoreHidden($input->getOption('hidden'));
        }
        if ($input->getOption('ignore-symlinks')) {
            $configuration->getFiles()->setIgnoreSymlinks($input->getOption('ignore-symlinks'));
        }
        if ($input->getOption('markers')) {
            $configuration->getParser()->setMarkers($input->getOption('ignore-symlinks'));
        }
        if ($input->getOption('title')) {
            $configuration->setTitle($input->getOption('title'));
        }
        if ($input->getOption('sourcecode')) {
            // $configuration->getParser()->setMarkers($input->getOption('visibility'));
        }
        if ($input->getOption('visibility')) {
            $configuration->getParser()->setVisibility(implode(',', $input->getOption('visibility')));
        }
        if ($input->getOption('defaultpackagename')) {
            $configuration->getParser()->setDefaultPackageName($input->getOption('defaultpackagename'));
        }
        if (! $configuration->getParser()->getVisibility()) {
            $configuration->getParser()->setVisibility('default');
        }
        if ($input->getOption('parseprivate')) {
            $configuration->getParser()->setVisibility($configuration->getParser()->getVisibility() . ',internal');
        }
        if ($input->getOption('force')) {
            $configuration->getParser()->setShouldRebuildCache(true);
        }

        // We add the files configuration because it should actually belong there, simplifies the interface but
        // removing it is a rather serious BC break. By using a non-serialized setter/property in the parser config
        // and setting the files config on it we can simplify this interface.
        $configuration->getParser()->setFiles($configuration->getFiles());

        return $configuration;
    }

    /**
     * Adds the parser.file.pre event to the advance the progressbar.
     *
     * @param InputInterface $input
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
            'parser.file.pre',
            function () use ($progress) {
                $progress->advance();
            }
        );

        return $progress;
    }

    /**
     * Translates the provided text and replaces any contained parameters using printf notation.
     *
     * @param string   $text
     * @param string[] $parameters
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    protected function __($text, $parameters = array())
    // @codingStandardsIgnoreEnd
    {
        return vsprintf($this->translator->translate($text), $parameters);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $numberOfFiles
     * @return ProgressHelper
     */
    private function startProgressbar(InputInterface $input, OutputInterface $output, $numberOfFiles)
    {
        /** @var ProgressHelper $progress */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->getHelper('phpdocumentor_logger')->connectOutputToLogging($output, $this);
        }

        if ($progress) {
            $progress->start($output, $numberOfFiles);
            return $progress;
        }
        return $progress;
    }

    /**
     * @param $progress
     */
    private function finishProgressbar($progress)
    {
        if ($progress) {
            $progress->finish();
        }
    }
}
