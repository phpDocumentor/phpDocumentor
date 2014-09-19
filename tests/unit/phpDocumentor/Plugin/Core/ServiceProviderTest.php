<?php

namespace phpDocumentor\Plugin\Core;

use \Mockery as m;
use Cilex\Application;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    private $fixture;
    
    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Application('test');
    }
    
    /**
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::register()
     */
    public function testRegister()
    {
        $serviceProvider = new ServiceProvider();
        
        $mockApplication = m::mock('Cilex\Application');
        $mockCollection = m::mock('phpDocumentor\Transformer\Writer\Collection');
        $mockApplication->shouldReceive('offsetGet')->with('transformer.writer.collection')->once()->andReturn($mockCollection);
        $mockApplication->shouldReceive('offsetGet')->with('monolog')->once()->andReturn(m::mock('Monolog\Logger'));
        
        $mockXml = m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
        $mockApplication->shouldReceive('offsetGet')->with('transformer.routing.standard')->once()->andReturn($mockXml);
        
        // translator
        $mockTranslator = m::mock('phpDocumentor\Translator\Translator');
        $mockApplication->shouldReceive('offsetGet')->with('translator')->once()->andReturn($mockTranslator);
        $mockTranslator->shouldReceive('addTranslationFolder')->with('/Messages$/')->once();
        $mockXml->shouldReceive('setTranslator')->with($mockTranslator)->once();
        
        // collection
        $mockCollection->shouldReceive('offsetSet')->with('FileIo', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\FileIo'))->once();
        $mockCollection->shouldReceive('offsetSet')->with('checkstyle', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\Checkstyle'))->once();
        $mockCollection->shouldReceive('offsetSet')->with('sourcecode', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\Sourcecode'))->once();
        $mockCollection->shouldReceive('offsetSet')->with('statistics', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\Statistics'))->once();
        $mockCollection->shouldReceive('offsetSet')->with('xml', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\Xml'))->once();
        $mockCollection->shouldReceive('offsetSet')->with('xsl', m::type('phpDocumentor\Plugin\Core\Transformer\Writer\Xsl'))->once();
        
        // set the translations
        $mockStyle = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Checkstyle');
        $mockStyle->shouldReceive('setTranslator')->with($mockTranslator)->once();
        $mockCollection->shouldReceive('offsetGet')->with('checkstyle')->once()->andReturn($mockStyle);
        $mockCollection->shouldReceive('offsetGet')->with('xml')->once()->andReturn($mockXml);
        
        // set the Xslt extensions
        $mockApplication->shouldReceive('offsetGet')->with('transformer.routing.queue')->once()->andReturn(m::mock('phpDocumentor\Transformer\Router\Queue'));
        $mockApplication->shouldReceive('offsetGet')->with('descriptor.builder')->once()->andReturn(m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder'));
        
        // register service providers
        $mockApplication->shouldReceive('register')->with(m::type('phpDocumentor\Plugin\Graphs\ServiceProvider'))->once();
        $mockApplication->shouldReceive('register')->with(m::type('phpDocumentor\Plugin\Twig\ServiceProvider'))->once();
        $mockApplication->shouldReceive('register')->with(m::type('phpDocumentor\Plugin\Pdf\ServiceProvider'))->once();

        $serviceProvider->register($mockApplication);
    }
}
