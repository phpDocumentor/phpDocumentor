<?php

namespace phpDocumentor\Translator;

use Cilex\Application;
use Mockery as m;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $locale = 'foobar';

    /** @var ServiceProvider $fixture */
    protected $fixture = null;

    /** @var Cilex\Application $application */
    protected $application = null;

    protected function setUp()
    {
        $this->application = new Application('test');
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
