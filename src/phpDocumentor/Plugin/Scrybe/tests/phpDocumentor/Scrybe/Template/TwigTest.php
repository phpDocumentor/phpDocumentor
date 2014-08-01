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

namespace phpDocumentor\Plugin\Scrybe\Template;

/**
 * Test for the Template\Factory class of phpDocumentor Scrybe.
 */
class TwigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::getName
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setName
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
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setName
     */
    public function testSetTemplateNameValidity($name, $erroneous)
    {
        if ($erroneous) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $fixture = new Twig();
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
        return array(
            array('1', true),
            array('12', true),
            array('123', false),
            array('Abc1', false),
            array('Abc-1', false),
            array('Abc-_1', false),
            array('Abc-_1!', true),
            array('Abc-_1 ', true),
            array(' Abc-_1', true),
        );
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::getExtension
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setExtension
     */
    public function testReadAndWriteExtension()
    {
        $fixture = new Twig();
        $this->assertEquals('html', $fixture->getExtension());
        $fixture->setExtension('pdf');
        $this->assertEquals('pdf', $fixture->getExtension());
    }

    /**
     * @dataProvider provideTestNamesForSetExtension
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setExtension
     */
    public function testSetExtensionValidity($extension, $erroneous)
    {
        if ($erroneous) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $fixture = new Twig();
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
        return array(
            array('1', true),
            array('12', false),
            array('pdf', false),
            array('html', false),
            array('latex', true),
            array('p-f', true),
            array('p_f', true),
        );
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::getPath
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setPath
     */
    public function testReadAndWriteBaseTemplatePath()
    {
        $fixture = new Twig();
        $this->assertEquals(
            realpath(__DIR__.'/../../../../data/templates'),
            $fixture->getPath()
        );
        $fixture->setPath(__DIR__);
        $this->assertEquals(__DIR__, $fixture->getPath());
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setPath
     * @expectedException InvalidArgumentException
     */
    public function testSetNonExistingBaseTemplatePath()
    {
        $fixture = new Twig();
        $fixture->setPath('bla');
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::setPath
     * @expectedException InvalidArgumentException
     */
    public function testSetFileAsBaseTemplatePath()
    {
        $fixture = new Twig();
        $fixture->setPath(__FILE__);
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Twig::getAssets
     */
    public function testGetAssets()
    {
        $fixture = new Twig('');
        $assets = $fixture->getAssets();

        $this->assertInternalType('array', $assets);
        $this->assertGreaterThan(0, count($assets));
        $this->assertInstanceOf('SplFileInfo', current($assets));
        $this->assertStringStartsWith($fixture->getPath(), key($assets));
        foreach (array_keys($assets) as $filename) {
            $this->assertStringEndsNotWith('.twig', $filename);
        }
    }
}
