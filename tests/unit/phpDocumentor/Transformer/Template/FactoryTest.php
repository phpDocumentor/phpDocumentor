<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template\Factory
 * @covers ::__construct
 * @covers ::<private>
 */
final class FactoryTest extends MockeryTestCase
{
    use Faker;

    /** @var Factory */
    private $fixture;
    /** @var m\LegacyMockInterface|m\MockInterface|FlySystemFactory */
    private $flySystemFactory;
    /** @var vfsStreamDirectory */
    private $globalTemplates;

    /**
     * Sets up the fixture with mocked dependencies.
     */
    protected function setUp() : void
    {
        $this->globalTemplates = vfsStream::setup('exampleDir');

        $this->flySystemFactory = $this->faker()->flySystemFactory();
        $this->fixture = new Factory($this->flySystemFactory, vfsStream::url('exampleDir'));
    }

    /**
     * @covers ::get
     * @covers ::createTemplateFromXml
     */
    public function testThatATemplateCanBeLoaded() : void
    {
        // Arrange
        $templateName = 'clean';
        $templateDirectory = $this->givenAnExampleTemplateInDirectoryCalled($templateName);
        $this->globalTemplates->addChild($templateDirectory);

        $templateCollection = m::mock(Collection::class);
        $templateCollection->shouldReceive('getTemplatesPath')->andReturn($this->globalTemplates->url());

        $transformer = new Transformer(
            $templateCollection,
            m::mock(WriterCollection::class),
            new NullLogger(),
            $this->flySystemFactory
        );
        $transformer->setTarget(vfsStream::path('exampleDir'));

        // Act
        $result = $this->fixture->get($transformer, $templateName);

        // Assert
        $this->assertSame($templateName, $result->getName());
        $this->assertSame('Mike van Riel <mike@phpdoc.org>', $result->getAuthor());
        $this->assertSame('1.0.0', $result->getVersion());
        $this->assertSame('Mike van Riel 2013', $result->getCopyright());
        $this->assertSame('This is the description', $result->getDescription());
        $this->assertEquals(['debug' => new Parameter('debug', 'on')], $result->getParameters());
        $this->assertSame(17, $result->count());
        $this->assertSame('copy', $result[0]->getQuery());
        $this->assertSame('FileIo', $result[0]->getWriter());
        $this->assertSame('templates/clean/htaccess.dist', $result[0]->getSource());
        $this->assertSame('.htaccess', $result[0]->getArtifact());
        $this->assertEquals(
            ['debug' => new Parameter('debug', 'on'), 'fakeParam' => new Parameter('fakeParam', 'value')],
            $result[0]->getParameters()
        );
    }

    /**
     * @covers ::get
     */
    public function testThatAnErrorOccuredWhenATemplateCannotBeFound() : void
    {
        $this->expectException(TemplateNotFound::class);

        // Arrange
        $templateName = 'does-not-exist';
        $templateCollection = m::mock(Collection::class);
        $templateCollection->shouldReceive('getTemplatesPath')->andReturn($this->globalTemplates->url());

        $transformer = new Transformer(
            $templateCollection,
            m::mock(WriterCollection::class),
            new NullLogger(),
            $this->flySystemFactory
        );
        $transformer->setTarget(vfsStream::path('exampleDir'));

        // Act
        $this->fixture->get($transformer, $templateName);
    }

    /**
     * @covers ::getTemplatesPath
     */
    public function testReturnTemplatePathFromConstructor() : void
    {
        // Act
        $result = $this->fixture->getTemplatesPath();

        // Assert
        $this->assertSame(vfsStream::url('exampleDir'), $result);
    }

    /**
     * @covers ::getAllNames
     */
    public function testRetrieveAllTemplateNames() : void
    {
        // Arrange
        $expected = ['template1', 'template2'];
        $this->globalTemplates->addChild(vfsStream::newDirectory($expected[0]));
        $this->globalTemplates->addChild(vfsStream::newFile('aFile.txt'));
        $this->globalTemplates->addChild(vfsStream::newDirectory($expected[1]));

        // Act
        $result = $this->fixture->getAllNames();

        // Assert
        $this->assertSame($expected, $result);
    }

    private function givenAnExampleTemplateInDirectoryCalled(string $templateName) : vfsStreamDirectory
    {
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<template>
  <name>$templateName</name>
  <author>Mike van Riel</author>
  <email>mike@phpdoc.org</email>
  <version>1.0.0</version>
  <copyright>Mike van Riel 2013</copyright>
  <description><![CDATA[This is the description]]></description>
  <parameter key="debug">on</parameter>
  <transformations>
    <transformation query="copy" writer="FileIo" source="templates/clean/htaccess.dist" artifact=".htaccess">
      <parameter key="fakeParam">value</parameter>
    </transformation>
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
        $templateDirectory = vfsStream::newDirectory('clean');
        $templateDirectory->addChild(vfsStream::newFile('template.xml')->setContent($xml));

        return $templateDirectory;
    }
}
