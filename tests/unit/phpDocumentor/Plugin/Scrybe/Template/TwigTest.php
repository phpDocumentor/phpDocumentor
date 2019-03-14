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

namespace phpDocumentor\Plugin\Scrybe\Template;

use org\bovigo\vfs\vfsStream;

/**
 * Test for the Template\Factory class of phpDocumentor Scrybe.
 * @coversDefaultClass phpDocumentor\Plugin\Scrybe\Template\Twig
 * @covers ::<<protected>>
 * @covers ::__construct
 */
class TwigTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers ::getName
     * @covers ::setName
     */
    public function testReadAndWriteTemplateName()
    {
        $fixture = new Twig('');
        $this->assertEquals('default', $fixture->getName());
        $fixture->setName('my_template-one');
        $this->assertEquals('my_template-one', $fixture->getName());
    }

    /**
     * @dataProvider provideTestNamesForSetName
     * @covers ::setName
     */
    public function testSetTemplateNameValidity($name, $erroneous)
    {
        if ($erroneous) {
            $this->expectException('\InvalidArgumentException');
        }

        $fixture = new Twig('');
        $fixture->setName($name);
        $this->assertEquals($name, $fixture->getName());
    }

    /**
     * Provides a dataset to test the different permutations of the template
     * name.
     *
     * @return string[][]
     */
    public function provideTestNamesForSetName()
    {
        return [
            ['1', true],
            ['12', true],
            ['123', false],
            ['Abc1', false],
            ['Abc-1', false],
            ['Abc-_1', false],
            ['Abc-_1!', true],
            ['Abc-_1 ', true],
            [' Abc-_1', true],
        ];
    }

    /**
     * @covers ::getExtension
     * @covers ::setExtension
     */
    public function testReadAndWriteExtension()
    {
        $fixture = new Twig('');
        $this->assertEquals('html', $fixture->getExtension());
        $fixture->setExtension('pdf');
        $this->assertEquals('pdf', $fixture->getExtension());
    }

    /**
     * @dataProvider provideTestNamesForSetExtension
     * @covers ::setExtension
     */
    public function testSetExtensionValidity($extension, $erroneous)
    {
        if ($erroneous) {
            $this->expectException('\InvalidArgumentException');
        }

        $fixture = new Twig('');
        $fixture->setExtension($extension);
        $this->assertEquals($extension, $fixture->getExtension());
    }

    /**
     * Provides a dataset to test the different permutations of the template
     * name.
     *
     * @return string[][]
     */
    public function provideTestNamesForSetExtension()
    {
        return [
            ['1', true],
            ['12', false],
            ['pdf', false],
            ['html', false],
            ['latex', true],
            ['p-f', true],
            ['p_f', true],
        ];
    }

    /**
     * @covers ::getPath
     * @covers ::setPath
     */
    public function testReadAndWriteBaseTemplatePath()
    {
        $fixture = new Twig('');
        $this->assertEquals(
            realpath(__DIR__ . '/../../../../data/templates'),
            $fixture->getPath()
        );
        $fixture->setPath(__DIR__);
        $this->assertEquals(__DIR__, $fixture->getPath());
    }

    /**
     * @covers ::setPath
     */
    public function testSetNonExistingBaseTemplatePath()
    {
        $this->expectException('InvalidArgumentException');
        $fixture = new Twig('');
        $fixture->setPath('bla');
    }

    /**
     * @covers ::setPath
     */
    public function testSetFileAsBaseTemplatePath()
    {
        $this->expectException('InvalidArgumentException');
        $fixture = new Twig('');
        $fixture->setPath(__FILE__);
    }

    /**
     * @covers ::getAssets
     */
    public function testGetAssets()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $fixture = new Twig(__DIR__ . '/../../../../../../src/phpDocumentor/Plugin/Scrybe/data/templates');
        $assets = $fixture->getAssets();

        $this->assertInternalType('array', $assets);
        $this->assertGreaterThan(0, count($assets));
        $this->assertInstanceOf('SplFileInfo', current($assets));
        $this->assertStringStartsWith($fixture->getPath(), key($assets));
        foreach (array_keys($assets) as $filename) {
            $this->assertStringEndsNotWith('.twig', $filename);
        }
    }

    /**
     * @covers ::decorate
     */
    public function testDecorate()
    {
        vfsStream::setup('root')
            ->addChild(vfsStream::newFile('default/layout.html.twig')
            ->withContent('{{ contents }}'));

        $fixture = new Twig('');
        $fixture->setPath('vfs://root');
        $result = $fixture->decorate('something');

        $this->assertEquals('something', $result);
    }

    /**
     * @covers ::decorate
     */
    public function testDecorateWhenTemplateCannotBeFound()
    {
        $this->expectException('DomainException');
        $this->expectExceptionMessage(
            'Template file "vfs://root' . DIRECTORY_SEPARATOR . 'default/layout.html.twig" could not be found'
        );
        vfsStream::setup('root');

        $fixture = new Twig('');
        $fixture->setPath('vfs://root');
        $fixture->decorate('something');
    }
}
