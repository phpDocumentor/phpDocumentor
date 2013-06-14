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

namespace phpDocumentor\Descriptor;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FileAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;
use Zend\Cache\Storage\Adapter\Filesystem;
use phpDocumentor\Descriptor\ProjectAnalyzer;

/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services to build the descriptors.
     *
     * This method injects the following services into the Dependency Injection Container:
     *
     * * descriptor.serializer, the serializer used to generate the cache
     * * descriptor.builder, the builder used to transform the Reflected information into a series of Descriptors.
     *
     * It is possible to override which serializer is used by overriding the parameter `descriptor.serializer.class`.
     *
     * @param Application $app An Application instance
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['descriptor.builder.serializer'] = 'PhpSerialize';

        $app['descriptor.cache'] = $app->share(
            function () {
                $cache = new Filesystem();
                $cache->setOptions(
                    array(
                         'namespace' => 'phpdoc-cache',
                         'cache_dir' => sys_get_temp_dir(),
                    )
                );
                $cache->addPlugin(new SerializerPlugin());
                return $cache;
            }
        );

        $app['descriptor.builder.assembler.factory'] = $app->share(
            function () {
                return new AssemblerFactory();
            }
        );

        $this->addReflectionAssemblers($app['descriptor.builder.assembler.factory']);

        $app['descriptor.builder.validator'] = $app->share(
            function ($container) {
                return new Validation($container['translator']);
            }
        );

        $app['descriptor.builder'] = $app->share(
            function ($container) {
                $builder = new ProjectDescriptorBuilder(
                    $container['descriptor.builder.assembler.factory'],
                    null, // TODO: Add filtering with the Zend\Filter Component
                    $container['descriptor.builder.validator']
                );

                return $builder;
            }
        );

        $app['descriptor.analyzer'] = function () {
            return new ProjectAnalyzer();
        };
    }

    /**
     * Registers the Assemblers used to convert Reflection objects to Descriptors.
     *
     * @param AssemblerFactory $factory
     *
     * @return void
     */
    protected function addReflectionAssemblers(AssemblerFactory $factory)
    {
        $factory->register(
            function ($criteria) {
                return $criteria instanceof FileReflector;
            },
            new FileAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof FunctionReflector;
            },
            new FunctionAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof ConstantReflector || $criteria instanceof ClassConstant;
            },
            new ConstantAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof ClassReflector;
            },
            new ClassAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof InterfaceReflector;
            },
            new InterfaceAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof TraitReflector;
            },
            new TraitAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof ClassReflector\PropertyReflector;
            },
            new PropertyAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof ClassReflector\MethodReflector;
            },
            new MethodAssembler()
        );
        $factory->register(
            function ($criteria) {
                return $criteria instanceof FunctionReflector\ArgumentReflector;
            },
            new ArgumentAssembler()
        );
    }
}
