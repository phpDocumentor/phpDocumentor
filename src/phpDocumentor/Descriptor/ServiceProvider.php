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
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\stripInternal;
use phpDocumentor\Descriptor\Filter\stripIgnore;
use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
use phpDocumentor\Descriptor\ProjectAnalyzer;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Validator;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;
use Zend\Cache\Storage\Adapter\Filesystem;

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
        if (!isset($app['validator.mapping.class_metadata_factory'])) {
            throw new Exception\MissingDependencyException(
                'The validator factory object that is used to validate the Descriptors is missing'
            );
        }

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

        $app['descriptor.filter'] = $app->share(
            function ($container) {
                return new Filter(new ClassFactory());
            }
        );

        $app['descriptor.builder'] = $app->share(
            function ($container) {
                $builder = new ProjectDescriptorBuilder(
                    $container['descriptor.builder.assembler.factory'],
                    $container['descriptor.filter'],
                    $container['validator']
                );

                return $builder;
            }
        );

        $app['descriptor.analyzer'] = function () {
            return new ProjectAnalyzer();
        };

        /** @var Validator $validator */
        $validator         = $app['validator'];
        $constantMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\ConstantDescriptor');
        $functionMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\FunctionDescriptor');
        $classMetadata     = $validator->getMetadataFor('phpDocumentor\Descriptor\ClassDescriptor');
        $interfaceMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\InterfaceDescriptor');
        $traitMetadata     = $validator->getMetadataFor('phpDocumentor\Descriptor\TraitDescriptor');
        $propertyMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\PropertyDescriptor');
        $methodMetadata    = $validator->getMetadataFor('phpDocumentor\Descriptor\MethodDescriptor');

        $classMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50005')));
        $propertyMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50007')));
        $methodMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50008')));
        $interfaceMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50009')));
        $traitMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50010')));
        $functionMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50011')));

        /** @var Filter $filter */
        $filter = $app['descriptor.filter'];
        $stripInternalFilter = new stripInternal($app['descriptor.builder']);
        $filter->attach('phpDocumentor\Descriptor\ConstantDescriptor', $stripInternalFilter);
        $filter->attach('phpDocumentor\Descriptor\FunctionDescriptor', $stripInternalFilter);
        $filter->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $stripInternalFilter);
        $filter->attach('phpDocumentor\Descriptor\TraitDescriptor', $stripInternalFilter);
        $filter->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripInternalFilter);
        $filter->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripInternalFilter);
        $stripIgnoreFilter = new stripIgnore($app['descriptor.builder']);
        $filter->attach('phpDocumentor\Descriptor\ConstantDescriptor', $stripIgnoreFilter);
        $filter->attach('phpDocumentor\Descriptor\FunctionDescriptor', $stripIgnoreFilter);
        $filter->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $stripIgnoreFilter);
        $filter->attach('phpDocumentor\Descriptor\TraitDescriptor', $stripIgnoreFilter);
        $filter->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripIgnoreFilter);
        $filter->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripIgnoreFilter);
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
        $factory->register(
            function ($criteria) {
                return $criteria instanceof FunctionReflector;
            },
            new FunctionAssembler()
        );
    }
}
