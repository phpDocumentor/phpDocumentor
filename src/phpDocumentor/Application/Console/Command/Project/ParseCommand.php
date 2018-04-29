<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console\Command\Project;

use League\Pipeline\PipelineInterface;
use phpDocumentor\Application\Console\Command\Command;
use phpDocumentor\Translator\Translator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
class ParseCommand extends Command
{
    private $pipeline;

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(PipelineInterface $pipeline, Translator $translator)
    {
        $this->pipeline = $pipeline;
        $this->translator = $translator;
        parent::__construct('project:parse');
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     */
    protected function configure()
    {
        // minimization of the following expression
        $VALUE_OPTIONAL_ARRAY = InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY;

        $this->setAliases(['parse'])
            ->setDescription($this->__('PPCPP-DESCRIPTION'))
            ->setHelp($this->__('PPCPP-HELPTEXT'))
            ->addOption('filename', 'f', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-FILENAME'))
            ->addOption('directory', 'd', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-DIRECTORY'))
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TARGET'))
            ->addOption('encoding', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-ENCODING'))
            ->addOption('extensions', null, $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-EXTENSIONS'))
            ->addOption('ignore', 'i', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-IGNORE'))
            ->addOption('ignore-tags', null, $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-IGNORETAGS'))
            ->addOption('hidden', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-HIDDEN'))
            ->addOption('ignore-symlinks', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-IGNORESYMLINKS'))
            ->addOption('markers', 'm', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-MARKERS'), ['TODO', 'FIXME'])
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TITLE'))
            ->addOption('force', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-FORCE'))
            ->addOption('validate', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-VALIDATE'))
            ->addOption('visibility', null, $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-VISIBILITY'))
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pipeLine = $this->pipeline;
        $pipeLine($input->getOptions());

        return 0;
    }

    /**
     * Translates the provided text and replaces any contained parameters using printf notation.
     *
     * @param string $text
     * @param string[] $parameters
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    private function __($text, $parameters = [])
    {
        // @codingStandardsIgnoreEnd
        return vsprintf($this->translator->translate($text), $parameters);
    }
}
