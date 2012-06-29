<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * The intention of this script is to be distributed as an independent source
 * file that can be used to install phpDocumentor in the current working
 * directory.
 *
 * The first part of this script contains the class with the utility methods
 * for the installation, followed by the actual script responsible for
 * calling the methods in the right order and informing the user.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 * Facilitates the installation of phpDocumentor.
 *
 * This class contains all methods used to download the latest stable version
 * of phpDocumentor, install dependencies via Composer and inform the user
 * what is going on.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class Installer
{
    /**
     * Downloads a zip archive of the master branch on Github into the current
     * working directory.
     *
     * @link https://github.com/phpDocumentor/phpDocumentor2/zipball/master
     *
     * @return void
     */
    public function downloadLatestPhpDocumentorArchive()
    {
        file_put_contents(
            sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpDocumentor-latest.zip',
            file_get_contents(
                'https://github.com/phpDocumentor/phpDocumentor2/zipball/master'
            )
        );
    }

    /**
     * Downloads a zip archive of the develop branch on Github into the current
     * working directory.
     *
     * Note: this is intended for testing purposes. It is strongly discouraged
     * to use this for production purposes.
     *
     * @link https://github.com/phpDocumentor/phpDocumentor2/zipball/develop
     *
     * @return void
     */
    public function downloadDevelopmentPhpDocumentorArchive()
    {
        file_put_contents(
            sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpDocumentor-latest.zip',
            file_get_contents(
                'https://github.com/phpDocumentor/phpDocumentor2/zipball/develop'
            )
        );
    }

    /**
     * Extracts the downloaded package into the current directory.
     *
     * This method also deletes the archive unless an error occurred.
     *
     * @see downloadLatestPhpDocumentorArchive()
     *
     * @throws \RuntimeException if the extraction process failed.
     *
     * @return void
     */
    public function extractPhpDocumentorToCurrentDirectory()
    {
        $zip = new \ZipArchive;
        if ($zip->open(sys_get_temp_dir().'/phpDocumentor-latest.zip') === true) {
            $root_dir = $zip->statIndex(0);
            if ($root_dir === false) {
                throw new \Exception(
                    'Zip file '. sys_get_temp_dir() . DIRECTORY_SEPARATOR
                    . 'phpDocumentor-latest.zip could not properly be extracted'
                );
            }
            $zip->extractTo(sys_get_temp_dir());

            // zipfile contains an unwanted root folder; copy all files one
            // level lower and delete unwanted root folder.
            $this->recursiveCopy(
                sys_get_temp_dir().DIRECTORY_SEPARATOR.$root_dir['name'], '.'
            );
            $this->recursiveRmdir(
                sys_get_temp_dir().DIRECTORY_SEPARATOR.$root_dir['name']
            );
            $zip->close();

            // delete package
            unlink(
                sys_get_temp_dir().DIRECTORY_SEPARATOR.'phpDocumentor-latest.zip'
            );
        } else {
            throw new \RuntimeException(
                'Unable to extract phpDocumentor\'s source code'
            );
        }
    }

    /**
     * Tests whether Composer can be found on this system.
     *
     * @return bool
     */
    public function testForComposer()
    {
        $output = array();
        $errorcode = null;
        exec('php composer.phar 2>&1', $output, $errorcode);

        return ($errorcode == 0);
    }

    /**
     * Download the installer for composer into the given folder.
     *
     * It is recommended to use the function sys_get_temp_dir() for the
     * installer_path. This will install composer in the tmp folder.
     *
     * @param string $installer_path the path where to put the installer.
     *
     * @return void
     */
    public function downloadComposerInstaller($installer_path)
    {
        file_put_contents(
            $installer_path.'/installer',
            file_get_contents('http://getcomposer.org/installer')
        );
    }

    /**
     * Installs composer in the given path.
     *
     * It is assumed that the downloadComposerInstaller() method is already used
     * to download the installer and that the same path is provided.
     * It is recommended to use the function sys_get_temp_dir() for the
     * installer_path. This will install composer in the tmp folder.
     *
     * This method is intended to provide a temporary installation for use with
     * phpDocumentor.
     *
     * @param string $installer_path Path where to install composer.
     *
     * @throws \Exception if the composer installer fails.
     *
     * @return string[]
     */
    public function installComposer($installer_path)
    {
        $output = array();
        $error_code = null;
        $composer_install_path = escapeshellarg($installer_path);
        $installer_path = escapeshellarg($installer_path.'/installer');

        exec(
            'php ' . $installer_path . ' --install-dir=' . $composer_install_path
            .' 2>&1',
            $output,
            $error_code
        );

        if ($error_code != 0) {
            throw new \Exception(
                'Unable to install composer itself; Composer returned: '
                .implode(PHP_EOL, $output)
            );
        }

        return $output;
    }

    /**
     * Invokes Composer to install all dependencies.
     *
     * @param string $installer_path Path to Composer, may be empty for a global
     *     Composer.
     *
     * @throws \Exception if the execution of Composer fails.
     *
     * @return array|null
     */
    public function installDependencies($installer_path)
    {
        $output = array();
        $error_code = null;

        $installer_path .= (($installer_path) ? DIRECTORY_SEPARATOR : '')
            .'composer.phar';
        exec('php ' . $installer_path . ' install 2>&1', $output, $error_code);

        if ($error_code != 0) {
            throw new \Exception(
                'Unable to install dependencies; Composer returned: '
                .implode(PHP_EOL, $output)
            );
        }

        return $output;
    }

    /**
     * Checks all dependencies that cannot be checked by Composer and informs
     * the user if one of them is absent.
     *
     * When a dependency fails then an entry in the result is added with the
     * error message; if the returned array is empty then no dependencies failed.
     *
     * This method should be moved to a separate Command in phpDocumentor itself.
     *
     * @return string[]
     */
    public function checkNonComposerDependencies()
    {
        $errors = array();
        if (!extension_loaded('xsl')) {
            $errors[] = 'It appears to the XSL extension for PHP is not enabled '
                .'(http://php.net/manual/en/book.xsl.php). Without it will '
                .'phpDocumentor be unable to generate HTML output';
        }

        $output     = '';
        $error_code = 0;
        exec('dot -V 2>&1', $output, $error_code);
        if ($error_code > 0) {
            $errors[] = 'The `dot` executable could not be found in your path. '
                .'Please make sure that you have installed GraphViz '
                .'(http://www.graphviz.org/Download..php) and make sure that '
                .'`dot` could be found in your path';
        }

        return $errors;
    }

    /**
     * Writes a message to the log with the given level of indentation.
     *
     * If no message is provided then only a whiteline is given.
     *
     * @param string $message Message to log.
     * @param int    $level   Indentation level.
     *
     * @return void.
     */
    public function log($message = '', $level = 0)
    {
        echo str_repeat(' ', $level * 2) . $message . PHP_EOL;
    }

    /**
     * Recursively copies the given folder to the destination.
     *
     * @param string $source      Path to a folder.
     * @param string $destination Path to the destination location (may exist)
     *
     * @throws \Exception If the destination location cannot be created or is
     *     not a directory.
     *
     * @return void
     */
    protected function recursiveCopy($source, $destination)
    {
        $source_handle = opendir($source);
        if (!file_exists($destination)) {
            mkdir($destination);
        }

        if (!is_dir($destination)) {
            throw new \Exception(
                'Destination "'.$destination.'" already exists and is a file or'
                .' cannot be created'
            );
        }

        while (false !== ($file = readdir($source_handle))) {
            if (($file == '.') || ($file == '..')) {
                continue;
            }

            $source_path      = $source . DIRECTORY_SEPARATOR . $file;
            $destination_path = $destination . DIRECTORY_SEPARATOR . $file;
            if (is_dir($source_path) ) {
                $this->recursiveCopy($source_path, $destination_path);
            } else {
                copy($source_path, $destination_path);
            }
        }

        closedir($source_handle);
    }

    /**
     * Recursively remove a directory and all contents.
     *
     * @param string $directory The directory to recursively delete.
     *
     * @return void
     */
    protected function recursiveRmdir($directory)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var \SplFileInfo $path */
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                rmdir($path->getPathname());
            } else {
                unlink($path->getPathname());
            }
        }
        rmdir($directory);
    }

}

$installer = new Installer();
try
{
    $installer->log('phpDocumentor installer for manual installations');

    // An IP was provided thus we set up proxying
    if (isset($argv[1]) && ($argv[1] != 'dev')) {
        // All HTTP requests are passed through the local NTLM proxy server.
        $r_default_context = stream_context_get_default(
            array(
                'http' => array('proxy' => $argv[1], 'request_fulluri' => true)
             )
        );

        // remove the second item in the array and reindex the keys
        array_splice($argv, 1, 1);

        // Though we said system wide, some extensions need a little coaxing.
        libxml_set_streams_context($r_default_context);
    }

    if (isset($argv[1]) && $argv[1] == 'dev') {
        $installer->log('> Downloading development application from Github');
        $installer->downloadDevelopmentPhpDocumentorArchive();
    } else {
        $installer->log('> Downloading application from Github');
        $installer->downloadLatestPhpDocumentorArchive();
    }

    $installer->log('> Extracting application');
    $installer->extractPhpDocumentorToCurrentDirectory();

    $installer->log('> Preparing dependencies');
    $composer_location = '';
    if (!$installer->testForComposer()) {
        // composer is not installed, install it to a temporary directory
        $composer_location = sys_get_temp_dir();
        $installer->log();
        $installer->log(
            'Composer (http://www.getcomposer.org) is not installed, downloading '
            .'temporary version for installation', 1
        );
        $installer->log();


        $installer->log(
            '> Downloading Composer installer to ' . $composer_location
        );
        $installer->downloadComposerInstaller($composer_location);

        $installer->log('> Installing composer to ' . $composer_location);
        $output = $installer->installComposer($composer_location);
        $installer->log();
        array_walk(
            $output,
            function($value) use ($installer) {
                $installer->log($value, 1);
            }
        );
        $installer->log();
    }

    $installer->log('> Installing dependencies');
    $output = $installer->installDependencies($composer_location);
    $installer->log();
    array_walk(
        $output,
        function($value) use ($installer) {
            $installer->log($value, 1);
        }
    );
    $installer->log();

    $installer->log('> Checking external dependencies');
    $errors = $installer->checkNonComposerDependencies();
    if (count($errors) == 0) {
        $installer->log('> Everything is OK');
    } else {
        $installer->log('> Dependency errors have been detected:');
        $installer->log();
        array_walk(
            $output,
            function($value) use ($installer) {
                $installer->log($value, 1);
            }
        );
        $installer->log();
    }
}
catch (\Exception $e)
{
    $installer->log('Error: ' . $e->getMessage());
    die(1);
}

$installer->log('> Thank you for installing phpDocumentor.');
$installer->log(
    '> You can run it using the command `php '.__DIR__.'/bin/phpdoc.php`'
);
