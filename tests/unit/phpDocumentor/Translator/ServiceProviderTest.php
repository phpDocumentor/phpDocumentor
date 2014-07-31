<?php

namespace phpDocumentor\Translator;

use Cilex\Application;
use Mockery as m;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceProvider $fixture */
    protected $fixture = null;

    /** @var Cilex\Application $application */
    protected $application = null;

    protected function setUp()
    {
        $this->application = new Application('test');
        $this->application['config'] = m::mock('phpDocumentor\Configuration');
        $translatorConfiguration = m::mock('phpDocumentor\Translator\Configuration');
        $translatorConfiguration->shouldReceive('getLocale')->andReturn('foobar');

        $this->application['config']->shouldReceive('getTranslator')
        ->andReturn($translatorConfiguration);
        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers phpDocumentor\Translator\ServiceProvider::register
     */
    public function testRegisterSetsTranslator()
    {
        $this->fixture->register($this->application);
        $translator = $this->application['translator'];

        $this->assertSame('foobar', $this->application['translator.locale']);
        $this->assertInstanceOf('phpDocumentor\Translator\Translator', $translator);
        $this->assertSame($this->application['translator.locale'], $translator->getLocale());
    }
}
