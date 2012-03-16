<?php
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
require_once('PEAR/PackageFileManager2.php');
PEAR::setErrorHandling(PEAR_ERROR_DIE);

function createPackager($original_file, $options = array())
{
  // merge the options with these defaults.
  $options = array_merge(array(
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
      'data/templates/*',
      'data/output/*',
      'data/log/*',
      'bin/package.php',
      'src/XHProf/*',     // Profiling package
    ),
    'exceptions'        => array(
      'bin/phpdoc.php'  => 'script',
      'bin/phpdoc.bat'  => 'script',
      'phpdoc.dist.xml' => 'php',
      'LICENSE'          => 'php',
      'phpunit.xml.dist' => 'php',
      'README'           => 'php',
    ),
    'installexceptions' => array(
      'bin/phpdoc.php' => '/',
      'bin/phpdoc.bat' => '/'
    ),
    'dir_roles'         => array(
      'bin'   => 'php',
      'docs'  => 'php',
      'data'  => 'php',
      'tests' => 'php',
      'src'   => 'php',
    ),
  ), $options);

  $packagexml = PEAR_PackageFileManager2::importOptions($original_file, $options);
  $packagexml->setPackageType('php');

  $packagexml->setPackage('phpDocumentor');
  $packagexml->setSummary('PHP 5.3 compatible API Documentation generator aimed at projects of all sizes and Continuous Integration');
  $packagexml->setDescription(<<<DESC
phpDocumentor is a Documentation Generation Application (DGA) for use with PHP applications.

It is capable of transforming the comments in your source code into a full API reference document.

phpDocumentor is build to be PHP 5.3 compatible, fast, having a low memory consumption and easily integratable into Continuous Integration.
DESC
  );
  $packagexml->setChannel('pear.phpdoc.org');
  $packagexml->setNotes('Please see the CHANGELOG in the root of the application for the latest changes');

  $packagexml->setPhpDep('5.2.6');
  $packagexml->setPearinstallerDep('1.4.0');
  $packagexml->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.0');
  $packagexml->addPackageDepWithChannel('required', 'phpDocumentor_Template_responsive', 'pear.phpdoc.org', '1.0.0');
  $packagexml->addPackageDepWithChannel('optional', 'PEAR_PackageFileManager2', 'pear.php.net', '1.0.2');

  $packagexml->addReplacement('bin/phpdoc.php', 'pear-config', '/usr/bin/env php', 'php_bin');
  $packagexml->addGlobalReplacement('pear-config', '@php_bin@', 'php_bin');
  $packagexml->addReplacement('bin/phpdoc.php', 'pear-config', '@php_dir@', 'php_dir');

  $packagexml->addMaintainer('lead', 'mvriel', 'Mike van Riel', 'mike.vanriel@naenius.com');
  $packagexml->setLicense('MIT', 'http://www.opensource.org/licenses/mit-license.html');

  // Add this as a release, and generate XML content
  $packagexml->addRelease();
  $packagexml->setOSInstallCondition('windows');
  $packagexml->addInstallAs('bin/phpdoc.bat', 'phpdoc.bat');
  $packagexml->addInstallAs('bin/phpdoc.php', 'phpdoc.php');

  $packagexml->addRelease();
  $packagexml->addInstallAs('bin/phpdoc.php', 'phpdoc');
  $packagexml->addIgnoreToRelease('bin/phpdoc.bat');

  return $packagexml;
}


echo 'phpDocumentor PEAR Packager v1.0'.PHP_EOL;

if ($argc < 3)
{
  echo <<<HELP

Usage:
  php package.php [version] [stability] [make|nothing]

Description:
  The phpDocumentor packager generates a package.xml file and accompanying package.
  By specifying the version and stability you can tell the packager which version to package.

  A file will only be generated if the third parameter is the word 'make'; otherwise the output will be send to
  the command line.

HELP;
  exit(0);
}

$packager = createPackager('../../package.xml');

$packager->setAPIVersion($argv[1]);
$packager->setReleaseVersion($argv[1]);
$packager->setReleaseStability($argv[2]);
$packager->setAPIStability($argv[2]);

$packager->generateContents();
if (isset($argv[3]) && ($argv[3] == 'make'))
{
  $packager->writePackageFile();
}
else
{
  $packager->debugPackageFile();
}
