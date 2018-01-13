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

use Mockery as m;

/**
 * Test for the Template\Factory class of phpDocumentor Scrybe.
 */
class FactoryTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Tests whether a Template can be registered using the constructor.
     *
     * @covers \phpDocumentor\Plugin\Scrybe\Template\Factory::__construct
     */
    public function testRegisterTemplateEngineViaConstructor()
    {
        $factory = new Factory(
            ['Mock' => m::mock('\phpDocumentor\Plugin\Scrybe\Template\Mock\Template')]
        );

        $this->assertInstanceOf(
            '\phpDocumentor\Plugin\Scrybe\Template\Mock\Template',
            $factory->get('Mock')
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
        $factory->register('Mock', m::mock('\phpDocumentor\Plugin\Scrybe\Template\Mock\Template'));
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
        $factory->register([], m::mock('\phpDocumentor\Plugin\Scrybe\Template\Mock\Template'));
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
}
