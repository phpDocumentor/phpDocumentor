<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console\Command;

use phpDocumentor\Application\Configuration\Factory\ConfigurationConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Converts a phpDocumentor2 configuration file to a phpDocumentor3 configuration file.
 */
class ConvertCommand extends Command
{
    /**
     * @var ConfigurationConverter
     */
    private $converter;

    public function __construct(ConfigurationConverter $converter)
    {
        parent::__construct('configuration:convert');

        $this->converter = $converter;
    }

    /**
     * Initializes this command and sets the name, description, options and arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription(
                'Converts a phpDocumentor2 configuration file to a phpDocumentor3 configuration file'
            )
            ->setHelp(
                <<<HELP
This task converts a phpDocumentor2 configuration file to a phpDocumentor3 configuration file.
HELP
            )
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Path where to find the original configuration file'
            )
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Path where to store the configuration file'
            );
    }

    /**
     * Converts a phpDocumentor2 configuration file to a phpDocumentor3 configuration file.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting conversion');

        $sourcePath = $input->getArgument('source');
        $targetPath = $input->getArgument('target');

        $priorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $source = $this->loadFile($sourcePath);

            $config = $this->converter->convertToLatestVersion($source);

            $this->saveFile($targetPath, $config->saveXML());

            $output->writeln('Finished conversion');
        } finally {
            libxml_use_internal_errors($priorSetting);
        }
    }

    /**
     * @param string $sourcePath
     *
     * @return \SimpleXMLElement
     * @throws \InvalidArgumentException
     */
    protected function loadFile($sourcePath)
    {
        $result = simplexml_load_string(file_get_contents($sourcePath));
        if ($result === false) {
            throw new \InvalidArgumentException(trim(libxml_get_last_error()->message));
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string $contents
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function saveFile($path, $contents)
    {
        $result = file_put_contents($path, $contents);

        if ($result === false) {
            throw new \InvalidArgumentException(trim(libxml_get_last_error()->message));
        }
    }
}
