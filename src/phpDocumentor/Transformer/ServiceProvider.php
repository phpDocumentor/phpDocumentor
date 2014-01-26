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
use phpDocumentor\Compiler\Pass\NamespaceTreeBuilder;
use phpDocumentor\Compiler\Pass\PackageTreeBuilder;
use phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor;
use phpDocumentor\Transformer\Command\Project\TransformCommand;
use phpDocumentor\Transformer\Command\Template\ListCommand;

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

        $templateDir = __DIR__ . '/../../../data/templates';
        // vendored installation
        if (file_exists(__DIR__ . '/../../../../templates')) {
            $templateDir = __DIR__ . '/../../../../templates';
        }

        // parameters
        $app['transformer.template.location'] = $templateDir;
        $app['linker.substitutions'] = array(
            'phpDocumentor\Descriptor\ProjectDescriptor'      => array('files'),
            'phpDocumentor\Descriptor\FileDescriptor'         => array('tags', 'classes', 'interfaces', 'traits'),
            'phpDocumentor\Descriptor\ClassDescriptor'        => array(
                'tags',
                'parent',
                'interfaces',
                'constants',
                'properties',
                'methods',
                'usedTraits',
            ),
            'phpDocumentor\Descriptor\InterfaceDescriptor'    => array(
                'tags',
                'parent',
                'constants',
                'methods',
            ),
            'phpDocumentor\Descriptor\TraitDescriptor'        => array(
                'tags',
                'properties',
                'methods',
                'usedTraits',
            ),
            'phpDocumentor\Descriptor\MethodDescriptor'       => array('tags', 'arguments'),
            'phpDocumentor\Descriptor\ArgumentDescriptor'     => array('types'),
            'phpDocumentor\Descriptor\PropertyDescriptor'     => array('tags', 'types'),
            'phpDocumentor\Descriptor\ConstantDescriptor'     => array('tags', 'types'),
            'phpDocumentor\Descriptor\Tag\ParamDescriptor'    => array('types'),
            'phpDocumentor\Descriptor\Tag\ReturnDescriptor'   => array('types'),
            'phpDocumentor\Descriptor\Tag\SeeDescriptor'      => array('reference'),
        );

        // services
        $app['compiler'] = $app->share(
            function ($container) {
                $compiler = new Compiler();
                $compiler->insert(new ElementsIndexBuilder(), ElementsIndexBuilder::COMPILER_PRIORITY);
                $compiler->insert(new MarkerFromTagsExtractor(), MarkerFromTagsExtractor::COMPILER_PRIORITY);
                $compiler->insert(new PackageTreeBuilder(), PackageTreeBuilder::COMPILER_PRIORITY);
                $compiler->insert(new NamespaceTreeBuilder(), NamespaceTreeBuilder::COMPILER_PRIORITY);
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
            function () {
                return new Router\StandardRouter();
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

        $app['transformer.template.collection'] = $app->share(
            function ($container) {
                return new Template\Collection(
                    $container['transformer.template.location'],
                    $container['serializer']
                );
            }
        );

        $app['transformer'] = $app->share(
            function ($container) {
                $transformer = new Transformer(
                    $container['transformer.template.collection'],
                    $container['transformer.writer.collection']
                );
                $transformer->setBehaviours($container['transformer.behaviour.collection']);

                return $transformer;
            }
        );

        $app->command(new TransformCommand($app['descriptor.builder'], $app['transformer'], $app['compiler']));
        $app->command(new ListCommand());
    }
}
