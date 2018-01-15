<?php
/**
* phpDocumentor
*
* PHP Version 5.3
*
* @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link http://phpdoc.org
*/

namespace phpDocumentor\Translator;

use Cilex\Application;
use Mockery as m;
use Symfony\Component\DependencyInjection\Container;

/**
 * Tests for phpDocumentor\Translator\ServiceProvider
 */
class ServiceProviderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Dummy locale.
     *
     * @var string
     */
    protected $locale = 'foobar';

    /** @var ServiceProvider $fixture */
    protected $fixture = null;

    /** @var Application $application */
    protected $application = null;

    /**
     * Setup test fixture and mocks used in this TestCase
     */
    protected function setUp()
    {
        $this->application = new Application(new Container());
        $this->application['config'] = m::mock('phpDocumentor\Configuration');

        $this->application['config']->shouldReceive('getTranslator->getLocale')
            ->andReturn($this->locale);
        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers phpDocumentor\Translator\ServiceProvider::register
     */
    public function testRegisterSetsTranslator()
    {
        $this->fixture->register($this->application);
        $translator = $this->application['translator'];

        $this->assertSame($this->locale, $this->application['translator.locale']);
        $this->assertInstanceOf('phpDocumentor\Translator\Translator', $translator);
        $this->assertSame($this->application['translator.locale'], $translator->getLocale());
    }
}
