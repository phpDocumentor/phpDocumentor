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

namespace phpDocumentor\Console\Command\Project;

use Exception;
use League\Pipeline\PipelineInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
final class TransformCommand extends Command
{
    /**
     * @var PipelineInterface
     */
    private $pipeline;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(PipelineInterface $pipeline)
    {
        parent::__construct('project:transform');
        $this->pipeline = $pipeline;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     */
    protected function configure(): void
    {
        $this->setAliases(['transform'])
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

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception if the provided source is not an existing file or a folder.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pipeLine = $this->pipeline;
        $pipeLine($input->getOptions());

        return 0;
    }
}
