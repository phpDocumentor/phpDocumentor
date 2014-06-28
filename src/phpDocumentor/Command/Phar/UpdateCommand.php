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
namespace phpDocumentor\Command\Phar;

use Herrera\Json\Exception\FileException;
use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Updates phpDocumentor.phar to the latest version.
 *
 *     $ php phpDocumentor.phar self-update
 */
class UpdateCommand extends Command
{
    const MANIFEST_FILE = 'https://raw.githubusercontent.com/phpDocumentor/phpDocumentor2/develop/manifest.json';

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('phar:update')
            ->setAliases(array('selfupdate', 'self-update'))
            ->setDescription(
                'Updates phpDocumentor.phar to the latest version'
            )
            ->addOption(
                'major',
                null,
                InputOption::VALUE_NONE,
                'Allow major version update'
            )
            ->addOption(
                'pre',
                null,
                InputOption::VALUE_NONE,
                'Allow pre-release version update'
            );
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
        $output->writeln('Looking for updates...');

         try {
             $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
         } catch (FileException $e) {
             $output->writeln('Unable to search for updates.');

             return 1;
         }

         $currentVersion  = $this->getApplication()->getVersion();

         $allowMajor      = $input->getOption('major');
         $allowPreRelease = $input->getOption('pre');

         if ($manager->update($currentVersion, $allowMajor, $allowPreRelease)) {
             $output->writeln('Updated to latest version.');
         } else {
             $output->writeln('Already up-to-date.');
         }

         return 0;
    }
}
