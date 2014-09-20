<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core;

use \Mockery as m;
use Cilex\Application;

/**
 * Tests whether all expected Services for the Core plugin are loaded using the ServiceProvider.
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceProvider */
    private $fixture;
    
    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new ServiceProvider();
    }
    
    /**
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::register
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::registerWriters
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::registerTranslationMessages
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::registerDependenciesOnXsltExtension
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::getTranslator
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::getWriterCollection
     */
    public function testRegister()
    {
        $mockCollection        = $this->givenAWriterCollection();
        $mockLogger            = $this->givenALogger();
        $mockApplication       = $this->givenAnApplication($mockCollection, $mockLogger);
        $mockRouterQueue       = $this->givenARouterQueue($mockApplication);
        $mockTranslator        = $this->givenATranslator($mockApplication);
        $mockDescriptorBuilder = $this->givenAProjectDescriptorBuilder($mockApplication);

        $this->thenATranslationFolderIsAdded($mockTranslator);

        $this->thenWritersAreRegistered($mockCollection);
        $this->thenRouterIsSetOnXmlWriter($mockApplication);
        $this->thenTranslatorIsSetOnWriter('Checkstyle', $mockTranslator, $mockCollection);
        $this->thenTranslatorIsSetOnWriter('Xml', $mockTranslator, $mockCollection);

        $this->thenProviderIsRegistered($mockApplication, 'phpDocumentor\Plugin\Graphs\ServiceProvider');
        $this->thenProviderIsRegistered($mockApplication, 'phpDocumentor\Plugin\Twig\ServiceProvider');
        $this->thenProviderIsRegistered($mockApplication, 'phpDocumentor\Plugin\Pdf\ServiceProvider');

        $this->fixture->register($mockApplication);

        $this->assertSame($mockRouterQueue, Xslt\Extension::$routers);
        $this->assertSame($mockDescriptorBuilder, Xslt\Extension::$descriptorBuilder);
    }

    /**
     * Creates and returns a mock of the Service Locator (application).
     *
     * @return m\MockInterface
     */
    private function givenAnApplication($mockCollection, $mockLogger)
    {
        $mockApplication = m::mock('Cilex\Application');

        $mockApplication->shouldReceive('offsetGet')
            ->once()
            ->with('transformer.writer.collection')
            ->andReturn($mockCollection);

        $mockApplication->shouldReceive('offsetGet')->once()->with('monolog')->andReturn($mockLogger);

        return $mockApplication;
    }

    /**
     * Returns a mock of the Writer Collection.
     *
     * @return m\MockInterface
     */
    private function givenAWriterCollection()
    {
        return m::mock('phpDocumentor\Transformer\Writer\Collection');
    }

    /**
     * Returns a mock of the Translator and instructs the Service Locator to return it.
     *
     * @param m\MockInterface $mockApplication
     *
     * @return m\MockInterface
     */
    private function givenATranslator($mockApplication)
    {
        $mockTranslator = m::mock('phpDocumentor\Translator\Translator');
        $mockApplication->shouldReceive('offsetGet')->with('translator')->andReturn($mockTranslator);

        return $mockTranslator;
    }

    /**
     * Instructs the mocked Collection to expect all writers in the plugin to be registered.
     *
     * @param m\MockInterface $mockCollection
     *
     * @return void
     */
    private function thenWritersAreRegistered($mockCollection)
    {
        $writerNamespace = 'phpDocumentor\Plugin\Core\Transformer\Writer\\';
        $this->thenWriterWasRegistered($mockCollection, 'FileIo', $writerNamespace . 'FileIo');
        $this->thenWriterWasRegistered($mockCollection, 'checkstyle', $writerNamespace . 'Checkstyle');
        $this->thenWriterWasRegistered($mockCollection, 'sourcecode', $writerNamespace . 'Sourcecode');
        $this->thenWriterWasRegistered($mockCollection, 'statistics', $writerNamespace . 'Statistics');
        $this->thenWriterWasRegistered($mockCollection, 'xml', $writerNamespace . 'Xml');
        $this->thenWriterWasRegistered($mockCollection, 'xsl', $writerNamespace . 'Xsl');
    }

    /**
     * Instructs the mocked Collection to expect an instance of the given className to be set on the given key.
     *
     * @param m\MockInterface $Collection
     * @param string          $key
     * @param string          $className
     *
     * @return void
     */
    private function thenWriterWasRegistered($Collection, $key, $className)
    {
        $Collection->shouldReceive('offsetSet')->with($key, m::type($className))->once();
    }

    /**
     * Creates and returns a mock of the Router Queue and instructs the Service Locator mock to return it on request,
     *
     * @param m\MockInterface $mockApplication
     *
     * @return m\MockInterface
     */
    private function givenARouterQueue($mockApplication)
    {
        $mockRouterQueue = m::mock('phpDocumentor\Transformer\Router\Queue');
        $mockApplication
            ->shouldReceive('offsetGet')
            ->with('transformer.routing.queue')
            ->once()
            ->andReturn($mockRouterQueue);

        return $mockRouterQueue;
    }

    /**
     * Creates and returns a mock of the ProjectDescriptorBuilder and instructs the Service Locator to return it on
     * request.
     *
     * @param m\MockInterface $mockApplication
     *
     * @return m\MockInterface
     */
    private function givenAProjectDescriptorBuilder($mockApplication)
    {
        $mockDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $mockApplication
            ->shouldReceive('offsetGet')
            ->with('descriptor.builder')
            ->once()
            ->andReturn($mockDescriptorBuilder);

        return $mockDescriptorBuilder;
    }

    /**
     * Instructs the ServiceLocator that an instance of the given Service Provider class will be registered on it.
     *
     * @param m\MockInterface $mockApplication
     * @param string          $serviceProviderMock
     *
     * @return void
     */
    private function thenProviderIsRegistered($mockApplication, $serviceProviderMock)
    {
        $mockApplication->shouldReceive('register')->with(m::type($serviceProviderMock))->once();
    }

    /**
     * Creates and returns a mock of the Logger.
     *
     * @return m\MockInterface
     */
    private function givenALogger()
    {
        return m::mock('Monolog\Logger');
    }

    /**
     * Instructs the translator to expect the 'Messages' folder to be set.
     *
     * @param m\MockInterface $mockTranslator
     *
     * @return void
     */
    private function thenATranslationFolderIsAdded($mockTranslator)
    {
        $mockTranslator->shouldReceive('addTranslationFolder')->with('/Messages$/')->once();
    }

    /**
     * Sets the expectations that the given writer is retrieved from the collection and it will have the given
     * translator assigned to it.
     *
     * @param string          $writerName
     * @param m\MockInterface $mockTranslator
     * @param m\MockInterface $mockCollection
     *
     * @return void
     */
    private function thenTranslatorIsSetOnWriter($writerName, $mockTranslator, $mockCollection)
    {
        $mockWriter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\\' . $writerName);
        $mockWriter->shouldReceive('setTranslator')->with($mockTranslator)->once();
        $mockCollection->shouldReceive('offsetGet')->with(strtolower($writerName))->once()->andReturn($mockWriter);
    }

    /**
     * Sets the expectations that a Router is set on the XML Writer.
     *
     * @param m\MockInterface $mockApplication
     *
     * @return void
     */
    private function thenRouterIsSetOnXmlWriter($mockApplication)
    {
        $mockRouter = m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
        $mockApplication
            ->shouldReceive('offsetGet')
            ->once()
            ->with('transformer.routing.standard')
            ->andReturn($mockRouter);
    }
}
