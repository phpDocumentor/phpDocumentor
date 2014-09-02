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

/**
 * Test class for \phpDocumentor\Bootstrap.
 *
 * @covers phpDocumentor\Bootstrap
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase
{
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
        $structure = array(
            'dummy' => array(
                'vendor' => array(),
                'src' => array(
                    'phpDocumentor' => array(),
                ),
                'test' => array(),
            ),
        );

        $root = \org\bovigo\vfs\vfsStream::setup('root', null, $structure);
        $bootstrap = Bootstrap::createInstance();

        $baseDir = \org\bovigo\vfs\vfsStream::url('root/dummy/src/phpDocumentor');
        $this->assertSame('vfs://root/dummy/src/phpDocumentor/../../vendor' ,$bootstrap->findVendorPath($baseDir));
    }

    /**
     * @covers phpDocumentor\Bootstrap::findVendorPath();
     */
    public function testFindVendorPathComposerInstalled()
    {
        $structure = array(
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

        $root = \org\bovigo\vfs\vfsStream::setup('root', null, $structure);
        \org\bovigo\vfs\vfsStream::newFile('composer.json')->at($root->getChild('dummy'));

        $bootstrap = Bootstrap::createInstance();
        $baseDir = \org\bovigo\vfs\vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../vendor'
            ,$bootstrap->findVendorPath($baseDir)
        );
    }
}
