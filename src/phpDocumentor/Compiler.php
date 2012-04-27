<?php

/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link http://phpdoc.org
 */

namespace phpDocumentor;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the phpDocumentor utility.
 *
 * It is heavy inspired by https://github.com/fabpot/Goutte/blob/master/src/Goutte/Compiler.php
 *
 * @author  Fabien Potencier <fabien@symfony.com>
 * @author  Gordon Franke <info@nevalon.de>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link http://phpdoc.org
 */
class Compiler
{
  public function compile($pharFile = 'phpDocumentor.phar')
  {
    if (file_exists($pharFile))
    {
      unlink($pharFile);
    }

    $phar = new \Phar($pharFile, 0, 'phpDocumentor');
    $phar->setSignatureAlgorithm(\Phar::SHA1);

    $phar->startBuffering();

    // CLI Component files
    foreach ($this->getFiles() as $file)
    {
      $path = str_replace(__DIR__.'/', '', $file);
      $content = preg_replace("#require_once 'Zend/.*?';#", '', php_strip_whitespace($file));

      $phar->addFromString($path, $content);
    }

    // Stubs
    $phar['_cli_stub.php'] = $this->getCliStub();
    $phar['_web_stub.php'] = $this->getWebStub();
    $phar->setDefaultStub('_cli_stub.php', '_web_stub.php');

    $phar->stopBuffering();

    // $phar->compressFiles(\Phar::GZ);

    unset($phar);
  }

  protected function getCliStub()
  {
    return "<?php ".$this->getLicense()." require_once __DIR__.'/bin/phpdoc.php'; __HALT_COMPILER();";
  }

  protected function getWebStub()
  {
    return "<?php throw new \LogicException('This PHAR file can only be used from the CLI.'); __HALT_COMPILER();";
  }

  protected function getLicense()
  {
    return '
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link http://phpdoc.org
 */';
  }

  protected function getFiles()
  {
    $files = array(
      'LICENSE',
    );

    $dirs = array(
      'bin',
      'data',
      'src',
      'vendor',
    );

    $finder = new Finder();
    $iterator = $finder->files()->in($dirs);

    return array_merge($files, iterator_to_array($iterator));
  }
}

