<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use phpDocumentor\Compiler\Pass\ClassTreeBuilder;
use phpDocumentor\Compiler\Pass\InterfaceTreeBuilder;
use Mockery as m;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use Pimple\Container;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Zend\Cache\Storage\StorageInterface;

/**
 * Tests for phpDocumentor\Translator\ServiceProvider
 * @coversDefaultClass \phpDocumentor\Transformer\ServiceProvider
 * @covers ::<protected>
 */
class ServiceProviderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ServiceProvider $fixture */
    protected $fixture;

    /** @var Container $container */
    protected $container;

    /**
     * Setup test fixture and mocks used in this TestCase
     */
    protected function setUp()
    {
        $this->container = new Container();

        $projectDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $serializer = m::mock('JMS\Serializer\Serializer');

        $transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $transformer->shouldReceive('getExternalClassDocumentation')->andReturn([]);

        $configuration = m::mock('\phpDocumentor\Configuration');
        $configuration->shouldReceive('getTransformer')->andReturn($transformer);

        $finder = new ExampleFinder();
        $logger = m::mock('\monolog\Logger');
        $analyzer = m::mock('\phpDocumentor\Descriptor\ProjectAnalyzer');

        $loggerHelper = m::mock('\phpDocumentor\Command\Helper\LoggerHelper');
        $loggerHelper->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelper->shouldReceive('setHelperSet');
        $loggerHelper->shouldReceive('addOptions');

        $this->container['descriptor.builder'] = $projectDescriptorBuilder;
        $this->container['serializer'] = $serializer;
        $this->container['config'] = $configuration;
        $this->container['parser.example.finder'] = $finder;
        $this->container['monolog'] = $logger;
        $this->container['descriptor.analyzer'] = $analyzer;
        $this->container['descriptor.cache'] = m::mock(StorageInterface::class);
        $this->container['console'] = new ConsoleApplication();
        $this->container['event_dispatcher'] = new EventDispatcher();
        $this->container['console']->getHelperSet()->set($loggerHelper);

        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers ::register
     */
    public function testRegisterSetsLinkerSubstitutions()
    {
        $this->fixture->register($this->container);

        $substitutions = $this->container['linker.substitutions'];
        $this->assertSame($substitutions, $this->givenLinkerSubstitutions());
    }

    /**
     * @covers ::register
     */
    public function testRegisterSetsCompiler()
    {
        $this->fixture->register($this->container);

        $compiler = $this->container->offsetGet('compiler');

        $this->assertInstanceOf('phpDocumentor\Compiler\Compiler', $compiler);
        $this->assertCount(11, $compiler);
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\ElementsIndexBuilder', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Linker\Linker', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\ResolveInlineLinkAndSeeTags', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\ExampleTagsEnricher', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\PackageTreeBuilder', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf(InterfaceTreeBuilder::class, $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\NamespaceTreeBuilder', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf(ClassTreeBuilder::class, $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Transformer\Transformer', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\Debug', $compiler->current());
    }

    /**
     * @covers ::register
     */
    public function testRegisterSetsLinker()
    {
        $this->fixture->register($this->container);

        $linker = $this->container->offsetGet('linker');

        $this->assertInstanceOf('phpDocumentor\Compiler\Linker\Linker', $linker);
        $this->assertSame($this->givenLinkerSubstitutions(), $linker->getSubstitutions());
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerBehaviourCollection()
    {
        $this->fixture->register($this->container);

        $collection = $this->container->offsetGet('transformer.behaviour.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Behaviour\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingStandard()
    {
        $this->fixture->register($this->container);

        $router = $this->container->offsetGet('transformer.routing.standard');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\StandardRouter', $router);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingExternal()
    {
        $this->fixture->register($this->container);

        $router = $this->container->offsetGet('transformer.routing.external');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\ExternalRouter', $router);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingQueue()
    {
        $this->fixture->register($this->container);

        $queue = $this->container->offsetGet('transformer.routing.queue');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\Queue', $queue);
        $this->assertSame($this->container->offsetGet('transformer.routing.external'), $queue->current());
        $queue->next();
        $this->assertSame($this->container->offsetGet('transformer.routing.standard'), $queue->current());
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerWriterCollection()
    {
        $this->fixture->register($this->container);

        $collection = $this->container->offsetGet('transformer.writer.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Writer\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateLocation()
    {
        $this->fixture->register($this->container);

        $location = $this->container->offsetGet('transformer.template.location');

        $this->assertSame('templates', substr($location, -9));
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplatePathResolver()
    {
        $this->fixture->register($this->container);

        $resolver = $this->container->offsetGet('transformer.template.path_resolver');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\PathResolver', $resolver);
        $this->assertSame('templates', substr($resolver->getTemplatePath(), -9));
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateFactory()
    {
        $this->fixture->register($this->container);

        $factory = $this->container->offsetGet('transformer.template.factory');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\Factory', $factory);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateCollection()
    {
        $this->fixture->register($this->container);

        $collection = $this->container->offsetGet('transformer.template.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformer()
    {
        $this->fixture->register($this->container);

        $transformer = $this->container->offsetGet('transformer');

        $this->assertInstanceOf('phpDocumentor\Transformer\Transformer', $transformer);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformCommand()
    {
        $this->fixture->register($this->container);

        $transformCommand = $this->container->offsetGet('console')->get('transform');

        $this->assertInstanceOf('phpDocumentor\Transformer\Command\Project\TransformCommand', $transformCommand);
    }

    /**
     * @covers ::register
     */
    public function testRegisterListCommand()
    {
        $this->fixture->register($this->container);

        $listCommand = $this->container->offsetGet('console')->get('template:list');

        $this->assertInstanceOf('phpDocumentor\Transformer\Command\Template\ListCommand', $listCommand);
    }

    /**
     * @covers ::register
     * @expectedException \phpDocumentor\Transformer\Exception\MissingDependencyException
     * @expectedExceptionMessage The builder object that is used to construct the ProjectDescriptor is missing
     */
    public function testRegisterThrowsExceptionIfBuilderIsMissing()
    {
        $this->fixture->register(new Container());
    }

    /**
     * @covers ::register
     * @expectedException \phpDocumentor\Transformer\Exception\MissingDependencyException
     * @expectedExceptionMessage The serializer object that is used to read the template configuration with is missing
     */
    public function testRegisterThrowsExceptionIfSerializerIsMissing()
    {
        $container = new Container();
        $projectDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $container['descriptor.builder'] = $projectDescriptorBuilder;

        $this->fixture->register($container);
    }

    private function givenLinkerSubstitutions()
    {
        $substitutions = array(
            'phpDocumentor\Descriptor\ProjectDescriptor'      => array('files'),
            'phpDocumentor\Descriptor\FileDescriptor'         => array(
                'tags',
                'classes',
                'interfaces',
                'traits',
                'functions',
                'constants'
            ),
            'phpDocumentor\Descriptor\ClassDescriptor'        => array(
                'tags',
                'parent',
                'interfaces',
                'constants',
                'properties',
                'methods',
                'usedTraits',
            ),
            'phpDocumentor\Descriptor\InterfaceDescriptor'       => array(
                'tags',
                'parent',
                'constants',
                'methods',
            ),
            'phpDocumentor\Descriptor\TraitDescriptor'           => array(
                'tags',
                'properties',
                'methods',
                'usedTraits',
            ),
            'phpDocumentor\Descriptor\FunctionDescriptor'        => array('tags', 'arguments'),
            'phpDocumentor\Descriptor\MethodDescriptor'          => array('tags', 'arguments'),
            'phpDocumentor\Descriptor\ArgumentDescriptor'        => array('types'),
            'phpDocumentor\Descriptor\PropertyDescriptor'        => array('tags', 'types'),
            'phpDocumentor\Descriptor\ConstantDescriptor'        => array('tags', 'types'),
            'phpDocumentor\Descriptor\Tag\ParamDescriptor'       => array('types'),
            'phpDocumentor\Descriptor\Tag\ReturnDescriptor'      => array('types'),
            'phpDocumentor\Descriptor\Tag\SeeDescriptor'         => array('reference'),
            'phpDocumentor\Descriptor\Tag\UsesDescriptor'        => array('reference'),
            'phpDocumentor\Descriptor\Type\CollectionDescriptor' => array('baseType', 'types', 'keyTypes'),
        );

        return $substitutions;
    }
}
