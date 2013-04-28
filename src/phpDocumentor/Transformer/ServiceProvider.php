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

namespace phpDocumentor\Transformer;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\Linker\Linker;
use phpDocumentor\Compiler\Pass\Debug;
use phpDocumentor\Compiler\Pass\ElementsIndexBuilder;
use phpDocumentor\Compiler\Pass\NamespaceTreeBuilder;
use phpDocumentor\Compiler\Pass\PackageTreeBuilder;
use phpDocumentor\Transformer\Command\Project\TransformCommand;
use phpDocumentor\Transformer\Command\Template\GenerateCommand;
use phpDocumentor\Transformer\Command\Template\ListCommand;
use phpDocumentor\Transformer\Command\Template\PackageCommand;

/**
 * This provider is responsible for registering the transformer component with the given Application.
 */
class ServiceProvider extends \stdClass implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
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
        $app['transformer.template.location'] = __DIR__ . '/../../../data/templates';
        $app['linker.substitutions'] = array(
            'phpDocumentor\Descriptor\ProjectDescriptor'      => array('files'),
            'phpDocumentor\Descriptor\FileDescriptor'         => array('classes'),
            'phpDocumentor\Descriptor\ClassDescriptor'        => array(
                'parent',
                'interfaces',
                'methods',
                'properties',
                'constants'
            ),
            'phpDocumentor\Descriptor\MethodDescriptor'       => array('tags', 'arguments'),
            'phpDocumentor\Descriptor\ArgumentDescriptor'     => array('types'),
            'phpDocumentor\Descriptor\PropertyDescriptor'     => array('types'),
            'phpDocumentor\Descriptor\ConstantDescriptor'     => array('types'),
            'phpDocumentor\Descriptor\Tag\ParamDescriptor'    => array('types'),
            'phpDocumentor\Descriptor\Tag\ReturnDescriptor'   => array('types'),
        );

        // services
        $app['compiler'] = $app->share(
            function ($container) {
                $compiler = new Compiler();
                $compiler->insert(new ElementsIndexBuilder(), ElementsIndexBuilder::COMPILER_PRIORITY);
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

        $app['transformer.routing.queue'] = $app->share(
            function () {
                $queue = new Router\Queue();

                // TODO: load from app configuration instead of hardcoded
                $queue->insert(new Router\StandardRouter(), 10000);

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
        $app->command(new GenerateCommand());
        $app->command(new ListCommand());
        $app->command(new PackageCommand());
    }
}
