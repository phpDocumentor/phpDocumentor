<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core;

use Mockery as m;
use phpDocumentor\Transformer\Router\Queue;

/**
 * Tests whether all expected Services for the Core plugin are loaded using the ServiceProvider.
 */
final class ServiceProviderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ServiceProvider */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp()
    {
        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::register
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::registerWriters
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::registerDependenciesOnXsltExtension
     * @covers phpDocumentor\Plugin\Core\ServiceProvider::getWriterCollection
     */
    public function testRegister()
    {
        $mockCollection = $this->givenAWriterCollection();
        $mockLogger = $this->givenALogger();
        $mockApplication = $this->givenAnApplication($mockCollection, $mockLogger);
        $mockDescriptorBuilder = $this->givenAProjectDescriptorBuilder($mockApplication);

        $this->thenWritersAreRegistered($mockCollection);
        $this->thenRouterIsSetOnXmlWriter($mockApplication);

        $this->thenProviderIsRegistered($mockApplication, 'phpDocumentor\Plugin\Graphs\ServiceProvider');
        $this->thenProviderIsRegistered($mockApplication, 'phpDocumentor\Plugin\Twig\ServiceProvider');

        $this->fixture->register($mockApplication);

        $this->assertInstanceOf(Queue::class, Xslt\Extension::$routers);
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
     * Instructs the mocked Collection to expect all writers in the plugin to be registered.
     *
     * @param m\MockInterface $mockCollection
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
     */
    private function thenWriterWasRegistered($Collection, $key, $className)
    {
        $Collection->shouldReceive('offsetSet')->with($key, m::type($className))->once();
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
     * Sets the expectations that a Router is set on the XML Writer.
     *
     * @param m\MockInterface $mockApplication
     */
    private function thenRouterIsSetOnXmlWriter($mockApplication)
    {
        $mockRouter = m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
        $mockApplication
            ->shouldReceive('offsetGet')
            ->twice()
            ->with('transformer.routing.standard')
            ->andReturn($mockRouter);
    }
}
