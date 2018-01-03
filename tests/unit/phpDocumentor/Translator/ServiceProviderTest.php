<?php
/**
* phpDocumentor
*
* PHP Version 5.3
*
* @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link http://phpdoc.org
*/

namespace phpDocumentor\Translator;

use Cilex\Application;
use Mockery as m;
use Pimple\Container;

/**
 * Tests for phpDocumentor\Translator\ServiceProvider
 */
class ServiceProviderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Dummy locale.
     *
     * @var string $locale
     */
    protected $locale = 'foobar';

    /** @var ServiceProvider $fixture */
    protected $fixture = null;

    /** @var Container $container */
    protected $container = null;

    /**
     * Setup test fixture and mocks used in this TestCase
     */
    protected function setUp()
    {
        $this->container = new Container();
        $this->container['config'] = m::mock('phpDocumentor\Configuration');

        $this->container['config']->shouldReceive('getTranslator->getLocale')
        ->andReturn($this->locale);
        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers phpDocumentor\Translator\ServiceProvider::register
     */
    public function testRegisterSetsTranslator()
    {
        $this->fixture->register($this->container);
        $translator = $this->container['translator'];

        $this->assertSame($this->locale, $this->container['translator.locale']);
        $this->assertInstanceOf('phpDocumentor\Translator\Translator', $translator);
        $this->assertSame($this->container['translator.locale'], $translator->getLocale());
    }
}
