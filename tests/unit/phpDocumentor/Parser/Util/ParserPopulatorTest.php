<?php

namespace phpDocumentor\Parser\Util;

use Mockery as m;
use phpDocumentor\Application\Console\Command\Helper\ConfigurationHelper;

class ParserPopulatorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testItPopulateParserWithCorrectOptions()
    {
        $parser = m::mock('\phpDocumentor\Parser\Parser');
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $configurationHelper = m::mock(ConfigurationHelper::class);

        $populator = new ParserPopulator();
        $input->shouldReceive('getOption')->with('force')->andReturn('force');
        $ignoredTags = ['ignored-tags'];
        $input->shouldReceive('getOption')->with('ignore-tags')->andReturn($ignoredTags);
        $input->shouldReceive('getOption')->with('validate')->andReturn('validate');

        $configurationHelper->shouldReceive('getOption')->with(
            $input,
            'encoding',
            'parser/encoding'
        )->once()->andReturn('encoding');
        $markers = ['markers'];
        $configurationHelper->shouldReceive('getOption')->with(
            $input,
            'markers',
            'parser/markers',
            ['TODO', 'FIXME'],
            true
        )->once()->andReturn($markers);
        $configurationHelper->shouldReceive('getOption')->with(
            $input,
            'defaultpackagename',
            'parser/default-package-name'
        )->andReturn('default-package-name');

        $parser->shouldReceive('setForced')->once()->with('force');
        $parser->shouldReceive('setIgnoredTags')->once()->with($ignoredTags);
        $parser->shouldReceive('setValidate')->once()->with('validate');
        $parser->shouldReceive('setEncoding')->once()->with('encoding');
        $parser->shouldReceive('setMarkers')->once()->with($markers);
        $parser->shouldReceive('setDefaultPackageName')->once()->with('default-package-name');

        $populator->populate(
            $parser,
            $input,
            $configurationHelper
        );

        m::close();

        $this->assertTrue(true);
    }
}
