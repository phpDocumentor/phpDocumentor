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

namespace phpDocumentor\Command\Phar;

use \Exception;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use phpDocumentor\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Updates phpDocumentor.phar to the latest version.
 *
 * ```
 * $ php phpDocumentor.phar phar:update [-m|--major] [-p|--pre] [version]
 * ```
 */
class UpdateCommand extends Command
{
    const PHAR_URL = 'https://github.com/phpDocumentor/phpDocumentor2/releases/latest';

    /**
     * Initializes this command and sets the name, description, options and arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('phar:update')
            ->setAliases(array('selfupdate', 'self-update'))
            ->setDescription('Updates the binary with the latest version.')
            ->addOption(
                'rollback',
                null,
                InputOption::VALUE_NONE,
                'Rollsback the updated binary to the last version.'
            )
            ->addOption('pre', 'p', InputOption::VALUE_NONE, 'Allow pre-release version update');
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Looking for updates...');


        $allowPreRelease = $input->getOption('pre');

        if (PHP_VERSION_ID < 50600) {
            $message = 'Self updating is not available in PHP versions under 5.6.' . "\n";
            $message .= 'The latest version can be found at ' . self::PHAR_URL;
            $output->writeln(sprintf('<error>%s</error>', $message));
            return 1;
        } elseif (Application::VERSION === ('@package_version@')) {
            $output->writeln('<error>Self updating has been disabled in source version.</error>');
            return 1;
        }

        $exitCode = 1;

        $updater = new Updater();
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('phpdocumentor/phpDocumentor2');
        $updater->getStrategy()->setPharName('phpDocumentor.phar');
        $updater->getStrategy()->getCurrentLocalVersion(Application::$VERSION);

        if ($allowPreRelease) {
            $updater->getStrategy()->setStability(GithubStrategy::ANY);
        }

        try {
            if ($input->getOption('rollback')) {
                $output->writeln('Rolling back to previous version...');
                $result = $updater->rollback();
            } else {
                if (!$updater->hasUpdate()) {
                    $output->writeln('No new version available.');
                    return 0;
                }

                $output->writeln('Updating to newer version...');
                $result = $updater->update();
            }

            if ($result) {
                $new = $updater->getNewVersion();
                $old = $updater->getOldVersion();

                $output->writeln(sprintf('Updated from %s to %s', $old, $new));
                $exitCode = 0;
            }
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        return $exitCode;
    }
}
