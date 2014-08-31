<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\Linker\Linker;
use phpDocumentor\Compiler\Pass\Debug;
use phpDocumentor\Compiler\Pass\ElementsIndexBuilder;
use phpDocumentor\Compiler\Pass\ExampleTagsEnricher;
use phpDocumentor\Compiler\Pass\NamespaceTreeBuilder;
use phpDocumentor\Compiler\Pass\PackageTreeBuilder;
use phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor;
use phpDocumentor\Compiler\Pass\ResolveInlineLinkAndSeeTags;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Transformer\Command\Project\TransformCommand;
use phpDocumentor\Transformer\Command\Template\ListCommand;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Template\Factory;
use phpDocumentor\Transformer\Template\PathResolver;

/**
 * This provider is responsible for registering the transformer component with the given Application.
 */
class ServiceProvider extends \stdClass implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     *
     * @throws Exception\MissingDependencyException if the application does not have a descriptor.builder service.
     * @throws Exception\MissingDependencyException if the application does not have a serializer service.
     */
    public function register(Application $app)
    {
        if (!isset($app['descriptor.builder'])) {
            throw new Exception\MissingDependencyException(
                'The builder object that is used to construct the ProjectDescriptor is missing'
            );
        }
        if (!isset($app['serializer'])) {
            throw new Exception\MissingDependencyException(
                'The serializer object that is used to read the template configuration with is missing'
            );
        }

        // parameters
        $app['linker.substitutions'] = array(
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
            'phpDocumentor\Descriptor\Type\CollectionDescriptor' => array('baseType', 'types', 'keyTypes'),
        );

        // services
        $app['compiler'] = $app->share(
            function ($container) {
                $compiler = new Compiler();
                $compiler->insert(new ElementsIndexBuilder(), ElementsIndexBuilder::COMPILER_PRIORITY);
                $compiler->insert(new MarkerFromTagsExtractor(), MarkerFromTagsExtractor::COMPILER_PRIORITY);
                $compiler->insert(
                    new ExampleTagsEnricher($container['parser.example.finder']),
                    ExampleTagsEnricher::COMPILER_PRIORITY
                );
                $compiler->insert(new PackageTreeBuilder(), PackageTreeBuilder::COMPILER_PRIORITY);
                $compiler->insert(new NamespaceTreeBuilder(), NamespaceTreeBuilder::COMPILER_PRIORITY);
                $compiler->insert(
                    new ResolveInlineLinkAndSeeTags($container['transformer.routing.queue']),
                    ResolveInlineLinkAndSeeTags::COMPILER_PRIORITY
                );
                $compiler->insert($container['linker'], Linker::COMPILER_PRIORITY);
                $compiler->insert($container['transformer'], Transformer::COMPILER_PRIORITY);
                $compiler->insert(
                    new Debug($container['monolog'], $container['descriptor.analyzer']),
                    Debug::COMPILER_PRIORITY
                );

                return $compiler;
            }
        );

        $app['linker'] = $app->share(
            function ($app) {
                return new Linker($app['linker.substitutions']);
            }
        );

        $app['transformer.behaviour.collection'] = $app->share(
            function () {
                return new Behaviour\Collection();
            }
        );

        $app['transformer.routing.standard'] = $app->share(
            function ($container) {
                /** @var ProjectDescriptorBuilder $projectDescriptorBuilder */
                $projectDescriptorBuilder = $container['descriptor.builder'];

                return new Router\StandardRouter($projectDescriptorBuilder);
            }
        );
        $app['transformer.routing.external'] = $app->share(
            function ($container) {
                return new Router\ExternalRouter($container['config']);
            }
        );

        $app['transformer.routing.queue'] = $app->share(
            function ($container) {
                $queue = new Router\Queue();

                // TODO: load from app configuration instead of hardcoded
                $queue->insert($container['transformer.routing.external'], 10500);
                $queue->insert($container['transformer.routing.standard'], 10000);

                return $queue;
            }
        );

        $app['transformer.writer.collection'] = $app->share(
            function ($container) {
                return new Writer\Collection($container['transformer.routing.queue']);
            }
        );

        $this->provideTemplatingSystem($app);

        $app['transformer'] = $app->share(
            function ($container) {
                $transformer = new Transformer(
                    $container['transformer.template.collection'],
                    $container['transformer.writer.collection']
                );

                /** @var Behaviour\Collection $behaviourCollection */
                $behaviourCollection = $container['transformer.behaviour.collection'];
                Dispatcher::getInstance()->addListener(
                    Transformer::EVENT_PRE_TRANSFORM,
                    function (PreTransformEvent $event) use ($behaviourCollection) {
                        $behaviourCollection->process($event->getProject());
                    }
                );

                return $transformer;
            }
        );

        $app->command(new TransformCommand($app['descriptor.builder'], $app['transformer'], $app['compiler']));
        $app->command(new ListCommand($app['transformer.template.factory']));
    }

    /**
     * Initializes the templating system in the container.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function provideTemplatingSystem(Application $app)
    {
        $templateDir = __DIR__ . '/../../../data/templates';

        // when installed using composer the templates are in a different folder
        $composerTemplatePath = __DIR__ . '/../../../../templates';
        if (file_exists($composerTemplatePath)) {
            $templateDir = $composerTemplatePath;
        }

        // parameters
        $app['transformer.template.location'] = $templateDir;

        // services
        $app['transformer.template.path_resolver'] = $app->share(
            function ($container) {
                return new PathResolver($container['transformer.template.location']);
            }
        );

        $app['transformer.template.factory'] = $app->share(
            function ($container) {
                return new Factory(
                    $container['transformer.template.path_resolver'],
                    $container['serializer']
                );
            }
        );

        $app['transformer.template.collection'] = $app->share(
            function ($container) {
                return new Template\Collection(
                    $container['transformer.template.factory'],
                    $container['transformer.writer.collection']
                );
            }
        );
    }
}
