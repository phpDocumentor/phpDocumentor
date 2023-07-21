<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\Dsn;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template\Factory
 * @covers ::__construct
 * @covers ::<private>
 */
final class FactoryTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    private Factory $fixture;

    /** @var ObjectProphecy|FlySystemFactory */
    private $flySystemFactory;

    private vfsStreamDirectory $globalTemplates;

    /**
     * Sets up the fixture with mocked dependencies.
     */
    protected function setUp(): void
    {
        $this->globalTemplates = vfsStream::setup();
        $this->flySystemFactory = $this->faker()->flySystemFactory();
        $this->fixture = new Factory(
            new WriterCollection(
                [
                    'FileIo' => $this->givenWriterWithName('FileIo')->reveal(),
                    'twig' => $this->givenWriterWithName('twig')->reveal(),
                    'Graph' => $this->givenWriterWithName('Graph')->reveal(),
                ],
            ),
            $this->flySystemFactory,
            vfsStream::url('root'),
        );
    }

    /**
     * @covers ::getTemplates
     * @covers ::createTemplateFromXml
     */
    public function testThatATemplateCanBeLoaded(): void
    {
        // Arrange
        $templateName = 'default';
        $templateDirectory = $this->givenAnExampleTemplateInDirectoryCalled($templateName);
        $this->globalTemplates->addChild($templateDirectory);

        // Act
        $result = $this->fixture->getTemplates(
            [['name' => $templateName, 'parameters' => []]],
            $this->flySystemFactory->create(Dsn::createFromString('./build')),
        )[$templateName];

        // Assert
        $this->assertSame($templateName, $result->getName());
        $this->assertSame('Mike van Riel <mike@phpdoc.org>', $result->getAuthor());
        $this->assertSame('1.0.0', $result->getVersion());
        $this->assertSame('Mike van Riel 2013', $result->getCopyright());
        $this->assertSame('This is the description', $result->getDescription());
        $this->assertEquals(['debug' => new Parameter('debug', 'on')], $result->getParameters());
        $this->assertSame(17, $result->count());
        $this->assertSame('$.copy', $result[0]->getQuery());
        $this->assertSame('FileIo', $result[0]->getWriter());
        $this->assertSame('templates/clean/htaccess.dist', $result[0]->getSource());
        $this->assertSame('.htaccess', $result[0]->getArtifact());
        $this->assertEquals(
            ['debug' => new Parameter('debug', 'on'), 'fakeParam' => new Parameter('fakeParam', 'value')],
            $result[0]->getParameters(),
        );
    }

    /** @covers ::getTemplates */
    public function testThatAnErrorOccuredWhenATemplateCannotBeFound(): void
    {
        $this->expectException(TemplateNotFound::class);

        // Arrange
        $templateName = 'default';
        $templateDirectory = $this->givenAnExampleTemplateInDirectoryCalled($templateName);
        $this->globalTemplates->addChild($templateDirectory);

        // Act
        $this->fixture->getTemplates(
            [['name' => 'does-not-exist', 'parameters' => []]],
            $this->flySystemFactory->create(Dsn::createFromString('./build')),
        );
    }

    /** @covers ::getTemplatesPath */
    public function testReturnTemplatePathFromConstructor(): void
    {
        // Act
        $result = $this->fixture->getTemplatesPath();

        // Assert
        $this->assertSame(vfsStream::url('root'), $result);
    }

    /** @covers ::getAllNames */
    public function testRetrieveAllTemplateNames(): void
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

    private function givenAnExampleTemplateInDirectoryCalled(string $templateName): vfsStreamDirectory
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
  <parameters>
    <parameter key="debug">on</parameter>
  </parameters>
</template>
XML;
        $templateDirectory = vfsStream::newDirectory('default');
        $templateDirectory->addChild(vfsStream::newFile('template.xml')->setContent($xml));

        return $templateDirectory;
    }

    private function givenWriterWithName(string $name): ObjectProphecy
    {
        $writer = $this->prophesize(WriterAbstract::class);
        $writer->getName()->willReturn($name);
        $writer->checkRequirements();

        return $writer;
    }
}
