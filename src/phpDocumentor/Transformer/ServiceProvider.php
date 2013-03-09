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
use phpDocumentor\Command\Project\TransformCommand;
use phpDocumentor\Command\Template\GenerateCommand;
use phpDocumentor\Command\Template\ListCommand;
use phpDocumentor\Command\Template\PackageCommand;
use phpDocumentor\Compiler\Compiler;

/**
 * This provider is responsible for registering the transformer component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
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

        // services
        $app['compiler'] = $app->share(
            function ($container) {
                $compiler = new Compiler();
                $compiler->insert($container['transformer'], Transformer::COMPILER_PRIORITY);

                return $compiler;
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
