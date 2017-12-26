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

namespace phpDocumentor\Parser;

use Cilex\Application;
use League\Flysystem\MountManager;
use phpDocumentor\Infrastructure\FlySystemFactory;
use phpDocumentor\Infrastructure\Parser\FlySystemCollector;
use phpDocumentor\Infrastructure\Parser\SpecificationFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use phpDocumentor\Parser\Middleware\CacheMiddleware;
use phpDocumentor\Parser\Middleware\ErrorHandlingMiddleware;
use phpDocumentor\Parser\Middleware\StopwatchMiddleware;
use phpDocumentor\Parser\Middleware\EmittingMiddleware;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Translator\Translator;
use Stash\Driver\FileSystem;
use Stash\Pool;

/**
 * This provider is responsible for registering the parser component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Container|Application $app An Application instance
     *
     * @throws Exception\MissingDependencyException if the Descriptor Builder is not present.
     * @throws \Stash\Exception\RuntimeException
     *
     * @return void
     *
     */
    public function register(Container $app)
    {
        if (!isset($app['descriptor.builder'])) {
            throw new Exception\MissingDependencyException(
                'The builder object that is used to construct the ProjectDescriptor is missing'
            );
        }

        $app['parser.middleware.cache'] = function () {
            return new CacheMiddleware(
                new Pool(new FileSystem(['path' => 'build/api-cache']))
            );
        };

        $app['parser'] = function ($app) {
            $stopWatch = $app['kernel.stopwatch'];

            $strategies = [
                new Factory\Argument(new PrettyPrinter()),
                new Factory\Class_(),
                new Factory\Constant(new PrettyPrinter()),
                new Factory\DocBlock(DocBlockFactory::createInstance()),
                new Factory\Function_(),
                new Factory\Interface_(),
                new Factory\Method(),
                new Factory\Property(new PrettyPrinter()),
                new Factory\Trait_(),
                new Factory\File(
                    NodesFactory::createInstance(),
                    [
                        new StopwatchMiddleware(
                            $stopWatch
                        ),
                        $app['parser.middleware.cache'],
                        new EmittingMiddleware(),
                        new ErrorHandlingMiddleware()
                    ]
                )
            ];

            $parser = new Parser(
                new ProjectFactory($strategies),
                $stopWatch
            );

            return $parser;
        };

        /** @var Translator $translator */
        $translator = $app['translator'];
        $translator->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');

        $fileCollector = new FlySystemCollector(
            new SpecificationFactory(),
            new FlySystemFactory(new MountManager())
        );

        if ($app instanceof Application) {
            $app->command(
                new ParseCommand(
                    $app['descriptor.builder'],
                    $app['parser'],
                    $fileCollector,
                    $translator,
                    $app['descriptor.cache'],
                    $app['parser.example.finder'],
                    $app['partials']
                )
            );
        }
    }
}
