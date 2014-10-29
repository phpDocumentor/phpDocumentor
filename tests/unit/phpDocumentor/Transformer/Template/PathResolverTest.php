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

namespace phpDocumentor\Transformer\Template;

use org\bovigo\vfs\vfsStream;

class PathResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $templatePath = 'vfs://root/templatePath';
    
    /** @var PathResolver */
    protected $fixture;

    /**
     * Sets up the fixture with mocked dependency.
     */
    public function setUp()
    {
        $this->fixture = new PathResolver($this->templatePath);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\PathResolver::__construct
     */
    public function testIfDependencyIsCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->templatePath, 'templatePath', $this->fixture);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithInvalidAbsolutePath()
    {
        $this->givenAVirtualFileSystem(array());
        $this->fixture->resolve('vfs://root/myFolder/myTemplate');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithInvalidName()
    {
        $this->givenAVirtualFileSystem(array());
        $this->fixture->resolve('invalidName');
    }

    /**
     * @covers phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithValidAbsolutePath()
    {
        $this->givenAVirtualFileSystem(array('template.xml' => 'xml'));
        $this->assertSame(
            vfsStream::url('root/templatePath') . DIRECTORY_SEPARATOR . 'myTemplate',
            $this->fixture->resolve('vfs://root/myFolder/myTemplate')
        );
    }

    /**
     * @covers phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithValidName()
    {
        $this->givenAVirtualFileSystem(array());
        $this->assertSame(
            vfsStream::url('root/templatePath') . DIRECTORY_SEPARATOR . 'Clean',
            $this->fixture->resolve('Clean')
        );
    }

    /**
     * @covers phpDocumentor\Transformer\Template\PathResolver::getTemplatePath
     */
    public function testGetTemplatePath()
    {
        $this->assertSame($this->templatePath, $this->fixture->getTemplatePath());
    }

    /**
     * Creates a virtual file system with a folder for $templatePath and
     * an folder as a location for a non-default template.
     * $template will either be an array that creates the template.xml in de virtual
     * file system structure in case of a test with a valid absolute path,
     * or an empty array
     */
    private function givenAVirtualFileSystem($template)
    {
        $structure = array(
            'templatePath' => array(
                'Clean' => array(
                    'template.xml' => 'xml'
                    )
                ),
            'myFolder' => array(
                'myTemplate' => $template
            )
        );
        vfsStream::setup('root');
        vfsStream::create($structure);
    }
}
