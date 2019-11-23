<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Transformer\Template;

class FactoryTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var m\MockInterface|PathResolver */
    private $pathResolverMock;

    /** @var Factory */
    private $fixture;

    /**
     * Sets up the fixture with mocked dependencies.
     */
    protected function setUp()
    {
        $this->pathResolverMock = m::mock('phpDocumentor\Transformer\Template\PathResolver');

        $this->fixture = new Factory($this->pathResolverMock);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::__construct
     */
    public function testIfDependenciesAreCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->pathResolverMock, 'pathResolver', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Factory::get
     * @covers phpDocumentor\Transformer\Template\Factory::fetchTemplateXmlFromPath
     * @covers phpDocumentor\Transformer\Template\Factory::createTemplateFromXml
     * @todo test parameters in template and transformations
     */
    public function testRetrieveInstantiatedTemplate()
    {
        // Arrange
        $templateName = 'clean';
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<template>
  <name>clean</name>
  <author>Mike van Riel</author>
  <email>mike@phpdoc.org</email>
  <version>1.0.0</version>
  <copyright>Mike van Riel 2013</copyright>
  <description><![CDATA[This is the description]]></description>
  <transformations>
    <transformation query="copy" writer="FileIo" source="templates/clean/htaccess.dist" artifact=".htaccess"/>
    <transformation query="copy" writer="FileIo" source="templates/clean/images" artifact="images"/>
    <transformation query="copy" writer="FileIo" source="templates/clean/css" artifact="css"/>
    <transformation query="copy" writer="FileIo" source="templates/clean/js" artifact="js"/>
    <transformation query="copy" writer="FileIo" source="templates/clean/font" artifact="font"/>
    <transformation writer="twig" query="namespace" source="templates/clean/namespace.html.twig" artifact="index.html"/>
    <transformation writer="twig" query="indexes.namespaces" source="templates/clean/namespace.html.twig" />
    <transformation writer="twig" query="indexes.classes" source="templates/clean/class.html.twig" />
    <transformation writer="twig" query="indexes.interfaces" source="templates/clean/interface.html.twig" />
    <transformation writer="twig" query="indexes.traits" source="templates/clean/class.html.twig" />
    <transformation writer="twig" query="files" source="templates/clean/file.html.twig" />
    <transformation 
        writer="twig" 
        query="files" 
        source="templates/clean/file.source.txt.twig" 
        artifact="files/{{path}}.txt"
    />
    <transformation writer="twig" source="templates/clean/reports/markers.html.twig" artifact="reports/markers.html"/>
    <transformation writer="twig" source="templates/clean/reports/errors.html.twig" artifact="reports/errors.html"/>
    <transformation 
        writer="twig" 
        source="templates/clean/reports/deprecated.html.twig" 
        artifact="reports/deprecated.html"
    />
    <transformation writer="twig" source="templates/clean/graphs/class.html.twig" artifact="graphs/class.html"/>
    <transformation writer="Graph" source="Class" artifact="graphs/classes.svg" />
  </transformations>
</template>
XML;
        vfsStream::setup('exampleDir')->addChild(vfsStream::newFile('template.xml')->setContent($xml));
        $this->pathResolverMock->shouldReceive('resolve')->with($templateName)->andReturn(vfsStream::url('exampleDir'));

        // Act
        $result = $this->fixture->get($templateName);

        // Assert
        $this->assertSame($templateName, $result->getName());
        $this->assertSame('Mike van Riel <mike@phpdoc.org>', $result->getAuthor());
        $this->assertSame('1.0.0', $result->getVersion());
        $this->assertSame('Mike van Riel 2013', $result->getCopyright());
        $this->assertSame('This is the description', $result->getDescription());
        $this->assertSame(17, $result->count());
        $this->assertSame('copy', $result[0]->getQuery());
        $this->assertSame('FileIo', $result[0]->getWriter());
        $this->assertSame('templates/clean/htaccess.dist', $result[0]->getSource());
        $this->assertSame('.htaccess', $result[0]->getArtifact());
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
        $expected = ['template1', 'template2'];
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
