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

use JMS\Serializer\Serializer;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Transformer\Template;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var m\MockInterface|PathResolver */
    private $pathResolverMock;

    /** @var m\MockInterface|Serializer */
    private $serializerMock;

    /** @var Factory */
    private $fixture;

    /**
     * Sets up the fixture with mocked dependencies.
     */
    public function setUp()
    {
        $this->pathResolverMock = m::mock('phpDocumentor\Transformer\Template\PathResolver');
        $this->serializerMock = m::mock('JMS\Serializer\Serializer');

        $this->fixture = new Factory($this->pathResolverMock, $this->serializerMock);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::__construct
     */
    public function testIfDependenciesAreCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->pathResolverMock, 'pathResolver', $this->fixture);
        $this->assertAttributeSame($this->serializerMock, 'serializer', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::get
     * @covers phpDocumentor\Transformer\Template\Factory::fetchTemplateXmlFromPath
     * @covers phpDocumentor\Transformer\Template\Factory::createTemplateFromXml
     */
    public function testRetrieveInstantiatedTemplate()
    {
        // Arrange
        $templateName = 'clean';
        $template = new Template($templateName);
        vfsStream::setup('exampleDir')->addChild(vfsStream::newFile('template.xml')->setContent('xml'));
        $this->pathResolverMock->shouldReceive('resolve')->with($templateName)->andReturn(vfsStream::url('exampleDir'));
        $this->serializerMock
            ->shouldReceive('deserialize')
            ->with('xml', 'phpDocumentor\Transformer\Template', 'xml')
            ->andReturn($template);

        // Act
        $result = $this->fixture->get($templateName);

        // Assert
        $this->assertSame($template, $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::getTemplatePath
     */
    public function testReturnTemplatePathFromResolver()
    {
        // Arrange
        $expected = 'test';
        $this->pathResolverMock->shouldReceive('getTemplatePath')->andReturn($expected);

        // Act
        $result = $this->fixture->getTemplatePath();

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::getAllNames
     */
    public function testRetrieveAllTemplateNames()
    {
        // Arrange
        $expected = array('template1', 'template2');
        $root = vfsStream::setup('exampleDir');
        $root->addChild(vfsStream::newDirectory($expected[0]));
        $root->addChild(vfsStream::newFile('aFile.txt'));
        $root->addChild(vfsStream::newDirectory($expected[1]));
        $this->pathResolverMock->shouldReceive('getTemplatePath')->andReturn(vfsStream::url('exampleDir'));

        // Act
        $result = $this->fixture->getAllNames();

        // Assert
        $this->assertSame($expected, $result);
    }
}
