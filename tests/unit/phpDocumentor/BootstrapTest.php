<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor;

use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

/**
 * Test class for \phpDocumentor\Bootstrap.
 *
 * @covers phpDocumentor\Bootstrap
 */
class BootstrapTest extends PHPUnit_Framework_TestCase
{
    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    protected $composerInstalledStructure = array(
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
     * Directory structure when phpdocumentor is installed from git.
     *
     * @var array
     */
    protected $standaloneStructure = array(
        'dummy' => array(
            'vendor' => array(),
            'src' => array(
                'phpDocumentor' => array(),
            ),
            'test' => array(),
        ),
    );

    /**
     * @covers phpDocumentor\Bootstrap::createInstance
     */
    public function testCreatingAnInstanceUsingStaticFactoryMethod()
    {
        $this->assertInstanceOf('phpDocumentor\Bootstrap', Bootstrap::createInstance());
    }

    /**
     * @covers phpDocumentor\Bootstrap::initialize
     */
    public function testInitializingTheApplication()
    {
        $bootstrap = Bootstrap::createInstance();
        $this->assertInstanceOf('phpDocumentor\Application', $bootstrap->initialize());
    }

    /**
     * @covers phpDocumentor\Bootstrap::findVendorPath();
     */
    public function testFindVendorPathStandAloneInstall()
    {
        vfsStream::setup('root', null, $this->standaloneStructure);
        $bootstrap = Bootstrap::createInstance();

        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        $this->assertSame('vfs://root/dummy/src/phpDocumentor/../../vendor', $bootstrap->findVendorPath($baseDir));
    }

    /**
     * @covers phpDocumentor\Bootstrap::findVendorPath();
     */
    public function testFindVendorPathComposerInstalled()
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructure);
        vfsStream::newFile('composer.json')->at($root->getChild('dummy'));

        $bootstrap = Bootstrap::createInstance();
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../vendor'
            , $bootstrap->findVendorPath($baseDir)
        );
    }
}
