<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
require_once('PEAR/PackageFileManager2.php');
\PEAR::setErrorHandling(PEAR_ERROR_DIE);

/**
 * Creates a packager object with all basic options set.
 *
 * @param string   $original_file Path of the original package.xml.
 * @param string[] $options       Set of options to merge in.
 *
 * @return PEAR_Error|PEAR_PackageFileManager2
 */
function createPackager($original_file, $options = array())
{
    // merge the options with these defaults.
    $options = array_merge(
        array(
            'packagefile'       => 'package.xml',
            'filelistgenerator' => 'file',
            'simpleoutput'      => true,
            'baseinstalldir'    => '/phpDocumentor',
            'packagedirectory'  => dirname(__FILE__).'/../../',
            'clearcontents'     => true,
            'ignore'            => array(
                'build.properties',
                'build.xml',
                'build/*',
                'data/output/*',
                'data/log/*',
                'bin/utils/*',
                'src/XHProf/*',     // Profiling package
                'vendor/twig/twig/ext/*'
            ),
            'exceptions'        => array(
                'bin/phpdoc'       => 'script',
                'bin/phpdoc.bat'   => 'script',
                'phpdoc.dist.xml'  => 'php',
                'LICENSE'          => 'php',
                'phpunit.xml.dist' => 'php',
                'README'           => 'php',
                'VERSION'          => 'php',
                'vendor/phpunit/phpunit-mock-objects/PHPUnit/Framework/MockObject/Autoload.php.in' => 'php',
                'vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php.in' => 'php',
                'vendor/phpunit/phpunit/PHPUnit/Autoload.php.in' => 'php',
                'vendor/phpunit/php-token-stream/PHP/Token/Stream/Autoload.php.in' => 'php',
                'vendor/phpunit/php-timer/PHP/Timer/Autoload.php.in' => 'php',
                'vendor/phpunit/php-text-template/Text/Template/Autoload.php.in' => 'php',
                'vendor/phpunit/php-file-iterator/File/Iterator/Autoload.php.in' => 'php',
                'vendor/phpunit/php-code-coverage/PHP/CodeCoverage/Autoload.php.in' => 'php',
            ),
            'installexceptions' => array(
                'bin/phpdoc' => '/',
                'bin/phpdoc.bat' => '/'
            ),
            'dir_roles'         => array(
                'bin'   => 'php',
                'docs'  => 'php',
                'data'  => 'php',
                'tests' => 'php',
                'src'   => 'php',
            ),
        ),
        $options
    );

    $packagexml = PEAR_PackageFileManager2::importOptions($original_file, $options);
    $packagexml->setPackageType('php');

    $packagexml->setPackage('phpDocumentor');
    $packagexml->setSummary(
        'PHP 5.3 compatible API Documentation generator aimed at projects of '
        .'all sizes and Continuous Integration'
    );
    $packagexml->setDescription(
<<<DESC
phpDocumentor is a Documentation Generation Application (DGA) for use with PHP applications.

It is capable of transforming the comments in your source code into a full API reference document.

phpDocumentor is build to be PHP 5.3 compatible, fast, having a low memory consumption and easily integratable into Continuous Integration.
DESC
    );
    $packagexml->setChannel('pear.phpdoc.org');
    $packagexml->setNotes(
        'Please see the CHANGELOG in the root of the application for the '
        .'latest changes'
    );

    $packagexml->setPhpDep('5.3.3');
    $packagexml->setPearinstallerDep('1.4.0');
    $packagexml->addReplacement(
        'bin/phpdoc',
        'pear-config',
        '/usr/bin/env php',
        'php_bin'
    );
    $packagexml->addGlobalReplacement('pear-config', '@php_bin@', 'php_bin');
    $packagexml->addReplacement(
        'bin/phpdoc',
        'pear-config',
        '@php_dir@',
        'php_dir'
    );

    $packagexml->addMaintainer(
        'lead',
        'mvriel',
        'Mike van Riel',
        'mike.vanriel@naenius.com'
    );
    $packagexml->addMaintainer(
        'lead',
        'ashnazg',
        'Chuck Burgess',
        'ashnazg@php.net'
    );
    $packagexml->setLicense(
        'MIT',
        'http://www.opensource.org/licenses/mit-license.html'
    );

    // Add this as a release, and generate XML content
    $packagexml->addRelease();
    $packagexml->setOSInstallCondition('windows');
    $packagexml->addInstallAs('bin/phpdoc.bat', 'phpdoc.bat');
    $packagexml->addInstallAs('bin/phpdoc',     'phpdoc');

    $packagexml->addRelease();
    $packagexml->addInstallAs('bin/phpdoc', 'phpdoc');
    $packagexml->addIgnoreToRelease('bin/phpdoc.bat');

    return $packagexml;
}

echo 'phpDocumentor PEAR Packager v1.0'.PHP_EOL;

if ($argc < 4) {
    echo <<<HELP

Usage:
  php package.php [version] [api-version] [stability] [make|nothing]

Description:
  The phpDocumentor packager generates a package.xml file and accompanying package.
  By specifying the version and stability you can tell the packager which version to package.

  A file will only be generated if the third parameter is the word 'make'; otherwise the output will be send to
  the command line.

HELP;
    exit(0);
}

$packager = createPackager('../../package.xml');

$packager->setReleaseVersion($argv[1]);
$packager->setAPIVersion($argv[2]);
$packager->setReleaseStability($argv[3]);
$packager->setAPIStability($argv[3]);

$packager->generateContents();
if (isset($argv[4]) && ($argv[4] == 'make')) {
    $packager->writePackageFile();
} else {
    $packager->debugPackageFile();
}
