<?php

namespace phpDocumentor\Translator;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Configuration $fixture */
    protected $fixture = null;

    protected function setUp()
    {
        $this->fixture = new Configuration();
    }

    /**
     * @covers phpDocumentor\Translator\Configuration::getLocale
     */
    public function testGetLocale()
    {
        $this->assertSame('en', $this->fixture->getLocale());
    }
}
