<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application;

use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass phpDocumentor\Application\Bootstrap
 * @covers ::<private>
 */
final class BootstrapTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    private $composerInstalledStructure = array(
        'dummy' => [
            'vendor' => [ 'phpDocumentor' => [ 'phpDocumentor' => [ 'src' => [ 'phpDocumentor' => [] ] ] ] ],
        ],
    );

    /**
     * Directory structure when phpdocumentor is installed from git.
     *
     * @var array
     */
    private $standaloneStructure = array(
        'dummy' => [
            'vendor' => [],
            'src' => [ 'phpDocumentor' => [] ],
            'test' => [],
        ],
    );

    /**
     * @covers ::createInstance
     */
    public function testCreatingAnInstanceUsingStaticFactoryMethod()
    {
        $this->assertInstanceOf('phpDocumentor\Application\Bootstrap', Bootstrap::createInstance());
    }

    /**
     * @covers ::initialize
     */
    public function testInitializingTheApplication()
    {
        $bootstrap = Bootstrap::createInstance();
        $this->assertInstanceOf('phpDocumentor\Application\Application', $bootstrap->initialize());
    }

    /**
     * @covers ::findVendorPath
     */
    public function testFindVendorPathStandAloneInstall()
    {
        vfsStream::setup('root', null, $this->standaloneStructure);
        $bootstrap = Bootstrap::createInstance();

        $baseDir = vfsStream::url('root/dummy/src/Application');
        $this->assertSame('vfs://root/dummy/src/Application/../../vendor', $bootstrap->findVendorPath($baseDir));
    }

    /**
     * @covers ::findVendorPath
     */
    public function testFindVendorPathComposerInstalled()
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructure);
        vfsStream::newFile('composer.json')->at($root->getChild('dummy'));

        $bootstrap = Bootstrap::createInstance();
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/Application');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/Application/../../../../../vendor',
            $bootstrap->findVendorPath($baseDir)
        );
    }

    /**
     * Tests if exception is thrown when no autoloader is present
     *
     * @covers ::createAutoloader
     */
    public function testCreateAutoloaderNoAutoloader()
    {
        $this->expectException(\RuntimeException::class);

        vfsStream::setup('root', null, $this->standaloneStructure);
        $bootstrap = Bootstrap::createInstance();
        $bootstrap->createAutoloader(vfsStream::url('root/dummy/vendor'));
    }

    /**
     * checks autoload.php is required and returned by createAutoloader
     *
     * @covers ::createAutoloader
     */
    public function testCreateAutoloader()
    {
        $root = vfsStream::setup('root', null, $this->standaloneStructure);
        vfsStream::newFile('autoload.php')->withContent('<?php return true;')
            ->at($root->getChild('dummy')->getChild('vendor'));

        $bootstrap = Bootstrap::createInstance();
        $this->assertTrue($bootstrap->createAutoloader(vfsStream::url('root/dummy/vendor')));
    }
}
