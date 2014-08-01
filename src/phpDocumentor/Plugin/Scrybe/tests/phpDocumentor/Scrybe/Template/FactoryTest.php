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
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether a Template can be registered using the constructor.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::__construct
     */
    public function testRegisterTemplateEngineViaConstructor()
    {
        $factory = new Factory(
            array('Mock' => '\phpDocumentor\Plugin\Scrybe\Template\Mock\Template')
        );

        $this->assertInstanceOf(
            '\phpDocumentor\Plugin\Scrybe\Template\Mock\Template',
            $factory->get('Mock')
        );
    }

    /**
     * Tests whether this factory registers the twig template engine by default.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory
     */
    public function testHasTwigTemplateEngine()
    {
        $factory = new Factory();
        $this->assertInstanceOf(
            '\phpDocumentor\Plugin\Scrybe\Template\Twig',
            $factory->get('twig')
        );
    }

    /**
     * Tests whether a Template could be registered using the register method.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::register
     */
    public function testRegisterTemplateEngine()
    {
        $factory = new Factory();
        $factory->register('Mock', '\phpDocumentor\Plugin\Scrybe\Template\Mock\Template');
        $this->assertInstanceOf(
            '\phpDocumentor\Plugin\Scrybe\Template\Mock\Template',
            $factory->get('Mock')
        );
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::register
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterInvalidName()
    {
        $factory = new Factory();
        $factory->register(array(), '');
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::register
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterInvalidClassName()
    {
        $factory = new Factory();
        $factory->register('', array());
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetUnknownTemplateEngine()
    {
        $factory = new Factory();
        $factory->get('Mock');
    }

    /**
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::get
     * @expectedException \RuntimeException
     */
    public function testGetInvalidTemplateEngine()
    {
        $factory = new Factory();
        $factory->register('Mock', '\DOMDocument');
        $factory->get('Mock');
    }
}
