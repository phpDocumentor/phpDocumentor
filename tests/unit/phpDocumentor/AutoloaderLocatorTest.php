<?php
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
 *
 *
 */

namespace phpDocumentor;

use Composer\Autoload\ClassLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class AutoloaderLocatorTest extends TestCase
{
    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    private $composerInstalledStructure = array(
        'dummy' => array(
            'vendor' => array(
                'phpDocumentor' => array(
                    'phpDocumentor' => array(
                        'src' => array(
                            'phpDocumentor' => array(),
                        ),
                    ),
                ),
            ),
        ),
    );

    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    private $customVendorDir = array(
        'dummy' => array(
            'custom-vendor' => array(
                'phpDocumentor' => array(
                    'phpDocumentor' => array(
                        'src' => array(
                            'phpDocumentor' => array(),
                        ),
                    ),
                ),
            ),
        ),
    );

    private $customVendorDirComposer = '{
    "config": {
        "vendor-dir": "custom-vendor"
    }
    }';


    /**
     * Directory structure when phpdocumentor is installed from git.
     *
     * @var array
     */
    private $standaloneStructure = array(
        'dummy' => array(
            'vendor' => array(),
            'src' => array(
                'phpDocumentor' => array(),
            ),
            'test' => array(),
        ),
    );


    public function testAutoloadAtDefaultLocation()
    {
        vfsStream::setup('root', null, $this->standaloneStructure);
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalled()
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructure);
        vfsStream::newFile('composer.json')->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalledCustomVendor()
    {
        $root = vfsStream::setup('root', null, $this->customVendorDir);
        vfsStream::newFile('composer.json')
            ->withContent($this->customVendorDirComposer)
            ->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }
}
