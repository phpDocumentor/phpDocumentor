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

use Cilex\Application;
use \phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Mockery as m;

/**
 * Tests for phpDocumentor\Translator\ServiceProvider
 * @coversDefaultClass \phpDocumentor\Transformer\ServiceProvider
 * @covers ::<protected>
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceProvider $fixture */
    protected $fixture = null;

    /** @var \Cilex\Application $application */
    protected $application = null;

    /**
     * Setup test fixture and mocks used in this TestCase
     */
    protected function setUp()
    {
        $this->application = new Application('test');

        $projectDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $serializer = m::mock('JMS\Serializer\Serializer');

        $transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $transformer->shouldReceive('getExternalClassDocumentation')->andReturn([]);

        $configuration = m::mock('\phpDocumentor\Configuration');
        $configuration->shouldReceive('getTransformer')->andReturn($transformer);

        $finder = m::mock('\phpDocumentor\Descriptor\Example\Finder');
        $logger = m::mock('\monolog\Logger');
        $analyzer = m::mock('\phpDocumentor\Descriptor\ProjectAnalyzer');

        $loggerHelper = m::mock('\phpDocumentor\Command\Helper\LoggerHelper');
        $loggerHelper->shouldReceive('getName')->andReturn('phpdocumentor_logger');
        $loggerHelper->shouldReceive('setHelperSet');
        $loggerHelper->shouldReceive('addOptions');

        $this->application['descriptor.builder'] = $projectDescriptorBuilder;
        $this->application['serializer'] = $serializer;
        $this->application['config'] = $configuration;
        $this->application['parser.example.finder'] = $finder;
        $this->application['monolog'] = $logger;
        $this->application['descriptor.analyzer'] = $analyzer;
        $this->application['console']->getHelperSet()->set($loggerHelper);

        $this->fixture = new ServiceProvider();
    }

    /**
     * @covers ::register
     */
    public function testRegisterSetsLinkerSubstitutions()
    {
        $this->fixture->register($this->application);

        $substitutions = $this->application['linker.substitutions'];
        $this->assertSame($substitutions, $this->givenLinkerSubstitutions());
    }

    /**
     * @covers ::register
     */
    public function testRegisterSetsCompiler()
    {
        $this->fixture->register($this->application);

        $compiler = $this->application->offsetGet('compiler');

        $this->assertInstanceOf('phpDocumentor\Compiler\Compiler', $compiler);
        $this->assertCount(9, $compiler);
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
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\NamespaceTreeBuilder', $compiler->current());
        $compiler->next();
        $this->assertInstanceOf('phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor', $compiler->current());
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
        $this->fixture->register($this->application);

        $linker = $this->application->offsetGet('linker');

        $this->assertInstanceOf('phpDocumentor\Compiler\Linker\Linker', $linker);
        $this->assertSame($this->givenLinkerSubstitutions(), $linker->getSubstitutions());
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerBehaviourCollection()
    {
        $this->fixture->register($this->application);

        $collection = $this->application->offsetGet('transformer.behaviour.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Behaviour\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingStandard()
    {
        $this->fixture->register($this->application);

        $router = $this->application->offsetGet('transformer.routing.standard');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\StandardRouter', $router);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingExternal()
    {
        $this->fixture->register($this->application);

        $router = $this->application->offsetGet('transformer.routing.external');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\ExternalRouter', $router);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerRoutingQueue()
    {
        $this->fixture->register($this->application);

        $queue = $this->application->offsetGet('transformer.routing.queue');

        $this->assertInstanceOf('phpDocumentor\Transformer\Router\Queue', $queue);
        $this->assertSame($this->application->offsetGet('transformer.routing.external'), $queue->current());
        $queue->next();
        $this->assertSame($this->application->offsetGet('transformer.routing.standard'), $queue->current());
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformerWriterCollection()
    {
        $this->fixture->register($this->application);

        $collection = $this->application->offsetGet('transformer.writer.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Writer\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateLocation()
    {
        $this->fixture->register($this->application);

        $location = $this->application->offsetGet('transformer.template.location');

        $this->assertSame('templates', substr($location, -9));
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplatePathResolver()
    {
        $this->fixture->register($this->application);

        $resolver = $this->application->offsetGet('transformer.template.path_resolver');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\PathResolver', $resolver);
        $this->assertSame('templates', substr($resolver->getTemplatePath(), -9));
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateFactory()
    {
        $this->fixture->register($this->application);

        $factory = $this->application->offsetGet('transformer.template.factory');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\Factory', $factory);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTemplateCollection()
    {
        $this->fixture->register($this->application);

        $collection = $this->application->offsetGet('transformer.template.collection');

        $this->assertInstanceOf('phpDocumentor\Transformer\Template\Collection', $collection);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformer()
    {
        $this->fixture->register($this->application);

        $transformer = $this->application->offsetGet('transformer');

        $this->assertInstanceOf('phpDocumentor\Transformer\Transformer', $transformer);
    }

    /**
     * @covers ::register
     */
    public function testRegisterTransformCommand()
    {
        $this->fixture->register($this->application);

        $transformCommand = $this->application->offsetGet('console')->get('transform');

        $this->assertInstanceOf('phpDocumentor\Transformer\Command\Project\TransformCommand', $transformCommand);
    }

    /**
     * @covers ::register
     */
    public function testRegisterListCommand()
    {
        $this->fixture->register($this->application);

        $listCommand = $this->application->offsetGet('console')->get('template:list');

        $this->assertInstanceOf('phpDocumentor\Transformer\Command\Template\ListCommand', $listCommand);
    }

    /**
     * @covers ::register
     * @expectedException \phpDocumentor\Transformer\Exception\MissingDependencyException
     * @expectedExceptionMessage The builder object that is used to construct the ProjectDescriptor is missing
     */
    public function testRegisterThrowsExceptionIfBuilderIsMissing()
    {
        $this->fixture->register(new Application('test'));
    }

    /**
     * @covers ::register
     * @expectedException \phpDocumentor\Transformer\Exception\MissingDependencyException
     * @expectedExceptionMessage The serializer object that is used to read the template configuration with is missing
     */
    public function testRegisterThrowsExceptionIfSerializerIsMissing()
    {
        $application = new Application('test');
        $projectDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $application['descriptor.builder'] = $projectDescriptorBuilder;

        $this->fixture->register($application);
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
