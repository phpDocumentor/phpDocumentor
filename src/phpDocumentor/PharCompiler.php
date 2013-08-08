<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Fabien Potencier <fabien@symfony.com>
 * @author    Gordon Franke <info@nevalon.de>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the phpDocumentor utility.
 *
 * It is inspired by Goutte's Phar Compiler
 * https://github.com/fabpot/Goutte/blob/master/src/Goutte/Compiler.php
 */
class PharCompiler
{
    /**
     * Compiles the source code into a PHAR file with the given name,
     *
     * @param string $pharFile
     *
     * @see getFiles() for the file selection process.
     *
     * @return void
     */
    public function compile($pharFile = 'phpDocumentor.phar')
    {
        $this->removeExistingPharArchive($pharFile);

        $phar = $this->initializePharArchive($pharFile);

        $phar->startBuffering();
        $this->addFilesToPharArchive($this->getFiles(), $phar);
        $this->addStubsToPharArchive($phar);
        $phar->stopBuffering();

        echo '>> Finished creating phar archive' . PHP_EOL;

        unset($phar);
    }

    /**
     * Detects whether the given PHAR file alrready exists and removes it if so.
     *
     * @param string $pharFile
     *
     * @return void
     */
    protected function removeExistingPharArchive($pharFile)
    {
        echo '>> Checking whether a phar file has been left from a previous run'
            . PHP_EOL;

        if (file_exists($pharFile)) {
            echo '>>   Removing previous phar' . PHP_EOL;
            unlink($pharFile);
        }
    }

    /**
     * Initializes a new PHAR archive with the given name.
     *
     * The PHAR archive's Signature's algorithm is set to SHA1.
     *
     * @param string $pharFile
     *
     * @return \Phar
     */
    protected function initializePharArchive($pharFile)
    {
        echo '>> Initializing new phar archive' . PHP_EOL;
        $phar = new \Phar($pharFile, 0, 'phpDocumentor');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        return $phar;
    }

    /**
     * Retrieves an array containing all filenames that are to be included.
     *
     * The following files are added:
     *
     * - LICENCE
     * - README.md
     * - bin/*
     * - data/* (excluding the output folder)
     * - src/*
     * - vendor/*
     *
     * @return string[]
     */
    protected function getFiles()
    {
        $files = array('LICENSE', 'README.md', 'VERSION');

        $finder = new Finder();
        $iterator = $finder->files()
            ->in(array('bin', 'data', 'src', 'vendor'))
            ->notName('*.rst')
            ->notName('*.md')
            ->exclude(
                array(
                    'output',
                    'behat',
                    'cilex/cilex/tests',
                    'dflydev/markdown/tests',
                    'nikic/php-parser/doc',
                    'nikic/php-parser/test',
                    'nikic/php-parser/test_old',
                    'phpdocumentor/fileset/tests',
                    'phpdocumentor/graphviz/tests',
                    'phpdocumentor/reflection-docblock/tests',
                    'pimple/pimple/tests',
                    'twig/twig/test',
                )
            );

        return array_merge($files, iterator_to_array($iterator));
    }

    /**
     * Processes the given array of filenames to be added to the PHAR archive.
     *
     * @param string[] $files
     * @param \Phar    $phar
     *
     * @return void
     */
    protected function addFilesToPharArchive(array $files, \Phar $phar)
    {
        echo '>> Found ' . count($files) . ' files to add to archive' . PHP_EOL;

        $counter = 0;
        foreach ($files as $file) {
            echo '.';
            $counter++;
            if ($counter % 70 == 0) {
                echo ' [' . $counter . '/' . count($files) . ']' . PHP_EOL;
            }
            $this->addFileToPharArchive($file, $phar);
        }
        echo PHP_EOL;
        echo '>> Finished adding files to archive' . PHP_EOL;
    }

    /**
     * Adds the given file to the PHAR archive,
     *
     * Before adding files to the archive they are bein converted to
     * relative paths.
     *
     * @param string $file
     * @param \Phar  $phar
     *
     * @return void
     */
    protected function addFileToPharArchive($file, \Phar $phar)
    {
        $path = str_replace(__DIR__ . '/', '', $file);
        $file_contents = file_get_contents($file);
        $file_contents = str_replace('#!/usr/bin/env php', '', $file_contents);
        $phar->addFromString($path, $file_contents);
    }

    /**
     * Adds the stubs for the CLI and Web interaction to the PHAR archive.
     *
     * @param \Phar $phar
     *
     * @see getCliStub() for the Stub for Command Line Interaction
     * @see getWebStub() for the Stub for Web Interaction
     *
     * @return void
     */
    protected function addStubsToPharArchive($phar)
    {
        $phar['_cli_stub.php'] = $this->getCliStub();
        $phar['_web_stub.php'] = $this->getWebStub();
        $phar->setDefaultStub('_cli_stub.php', '_web_stub.php');
    }

    /**
     * Adds a stub file that initiates the command line interaction.
     *
     * @return string
     */
    protected function getCliStub()
    {
        return <<<'PHP'
#!/usr/bin/env php
<?php
require_once __DIR__.'/bin/phpdoc.php'; __HALT_COMPILER();
PHP;
    }

    /**
     * Adds a stub file that initiates the web interaction.
     *
     * @return string
     */
    protected function getWebStub()
    {
        return <<<'PHP'
#!/usr/bin/env php
"<?php
throw new \LogicException('This PHAR file can only be used from the CLI.');
__HALT_COMPILER();
PHP;
    }

    /**
     * Returns a license file-level DocBlock for use in the Stubs.
     *
     * @return string
     */
    protected function getLicense()
    {
        return '
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */';
    }
}
