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
use phpDocumentor\Descriptor\Builder\Reflector\Tags\AuthorAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\GenericTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\LinkAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler as MethodTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ParamAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler as PropertyTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SinceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VersionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints as phpDocAssert;
use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;
use phpDocumentor\Reflection\DocBlock\Tag\LinkTag;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Tag\SinceTag;
use phpDocumentor\Reflection\DocBlock\Tag\ThrowsTag;
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;

/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services needed to build the descriptors.
     *
     * @param Application $app An Application instance
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->addCache($app);
        $this->addAssemblers($app);
        $this->addFilters($app);
        $this->addValidators($app);
        $this->addBuilder($app);

        // I would prefer to extend it but due to a circular reference will pimple fatal
        $this->attachFiltersToManager($app['descriptor.filter'], $app);

        $app['descriptor.analyzer'] = function () {
            return new ProjectAnalyzer();
        };
    }

    /**
     * Registers the Assemblers used to convert Reflection objects to Descriptors.
     *
     * @param AssemblerFactory $factory
     *
     * @return AssemblerFactory
     */
    public function attachAssemblersToFactory(AssemblerFactory $factory)
    {
        $fileMatcher      = function ($criteria) {return $criteria instanceof FileReflector; };
        $constantMatcher  = function ($criteria) {
            return $criteria instanceof ConstantReflector || $criteria instanceof ClassConstant;
        };
        $classMatcher     = function ($criteria) { return $criteria instanceof ClassReflector; };
        $interfaceMatcher = function ($criteria) { return $criteria instanceof InterfaceReflector; };
        $traitMatcher     = function ($criteria) { return $criteria instanceof TraitReflector; };
        $propertyMatcher  = function ($criteria) { return $criteria instanceof ClassReflector\PropertyReflector; };
        $methodMatcher    = function ($criteria) { return $criteria instanceof ClassReflector\MethodReflector; };
        $argumentMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector\ArgumentReflector; };
        $functionMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector; };

        $authorMatcher      = function ($criteria) { return $criteria instanceof AuthorTag; };
        $linkMatcher        = function ($criteria) { return $criteria instanceof LinkTag; };
        $methodTagMatcher   = function ($criteria) { return $criteria instanceof MethodTag; };
        $propertyTagMatcher = function ($criteria) { return $criteria instanceof PropertyTag; };
        $paramMatcher       = function ($criteria) { return $criteria instanceof ParamTag; };
        $throwsMatcher      = function ($criteria) { return $criteria instanceof ThrowsTag; };
        $returnMatcher      = function ($criteria) { return $criteria instanceof ReturnTag; };
        $usesMatcher        = function ($criteria) { return $criteria instanceof UsesTag; };
        $seeMatcher         = function ($criteria) { return $criteria instanceof SeeTag; };
        $sinceMatcher       = function ($criteria) { return $criteria instanceof SinceTag; };
        $varMatcher         = function ($criteria) { return $criteria instanceof VarTag; };
        $versionMatcher     = function ($criteria) { return $criteria instanceof Tag\VersionTag; };

        $tagFallbackMatcher = function ($criteria) { return $criteria instanceof Tag; };

        $factory->register($fileMatcher, new FileAssembler());
        $factory->register($constantMatcher, new ConstantAssembler());
        $factory->register($classMatcher, new ClassAssembler());
        $factory->register($interfaceMatcher, new InterfaceAssembler());
        $factory->register($traitMatcher, new TraitAssembler());
        $factory->register($propertyMatcher, new PropertyAssembler());
        $factory->register($methodMatcher, new MethodAssembler());
        $factory->register($argumentMatcher, new ArgumentAssembler());
        $factory->register($functionMatcher, new FunctionAssembler());

        $factory->register($authorMatcher, new AuthorAssembler());
        $factory->register($linkMatcher, new LinkAssembler());
        $factory->register($methodTagMatcher, new MethodTagAssembler());
        $factory->register($propertyTagMatcher, new PropertyTagAssembler());
        $factory->register($paramMatcher, new ParamAssembler());
        $factory->register($throwsMatcher, new ThrowsAssembler());
        $factory->register($returnMatcher, new ReturnAssembler());
        $factory->register($usesMatcher, new UsesAssembler());
        $factory->register($seeMatcher, new SeeAssembler());
        $factory->register($sinceMatcher, new SinceAssembler());
        $factory->register($varMatcher, new VarAssembler());
        $factory->register($versionMatcher, new VersionAssembler());

        $factory->registerFallback($tagFallbackMatcher, new GenericTagAssembler());

        return $factory;
    }

    /**
     * Attaches filters to the manager.
     *
     * @param Filter $filterManager
     * @param Application $app
     *
     * @return Filter
     */
    public function attachFiltersToManager(Filter $filterManager, Application $app)
    {
        $filtersOnAllDescriptors = array(
            new StripInternal($app['descriptor.builder']),
            new StripIgnore($app['descriptor.builder'])
        );

        foreach ($filtersOnAllDescriptors as $filter) {
            $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $filter);
        }

        return $filterManager;
    }

    /**
     * Adds validators to check the Descriptors.
     *
     * @param Validator $validator
     *
     * @return Validator
     */
    public function attachValidators(Validator $validator)
    {
        /** @var ClassMetadata $fileMetadata */
        $fileMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\FileDescriptor');
        /** @var ClassMetadata $constantMetadata */
        $constantMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\ConstantDescriptor');
        /** @var ClassMetadata $functionMetadata */
        $functionMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\FunctionDescriptor');
        /** @var ClassMetadata $classMetadata */
        $classMetadata     = $validator->getMetadataFor('phpDocumentor\Descriptor\ClassDescriptor');
        /** @var ClassMetadata $interfaceMetadata */
        $interfaceMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\InterfaceDescriptor');
        /** @var ClassMetadata $traitMetadata */
        $traitMetadata     = $validator->getMetadataFor('phpDocumentor\Descriptor\TraitDescriptor');
        /** @var ClassMetadata $propertyMetadata */
        $propertyMetadata  = $validator->getMetadataFor('phpDocumentor\Descriptor\PropertyDescriptor');
        /** @var ClassMetadata $methodMetadata */
        $methodMetadata    = $validator->getMetadataFor('phpDocumentor\Descriptor\MethodDescriptor');

        $fileMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50000')));
        $classMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50005')));
        $propertyMetadata->addConstraint(new phpDocAssert\Property\HasSummary());
        $methodMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50008')));
        $interfaceMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50009')));
        $traitMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50010')));
        $functionMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50011')));

        $functionMetadata->addGetterConstraint(
            'response',
            new Assert\NotEqualTo(array('value' => 'type', 'message' => 'PPC:ERR-50017'))
        );
        $methodMetadata->addGetterConstraint(
            'response',
            new Assert\NotEqualTo(array('value' => 'type', 'message' => 'PPC:ERR-50017'))
        );

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasSinglePackage());

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage());

        $classMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $interfaceMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $traitMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());
        $fileMetadata->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage());

        return $validator;
    }

    /**
     * Adds the caching mechanism to the dependency injection container with key 'descriptor.cache'.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addCache(Application $app)
    {
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
    }

    /**
     * Adds the Building mechanism using the key 'descriptor.builder'.
     *
     * Please note that the type of serializer can be configured using the parameter 'descriptor.builder.serializer'; it
     * accepts any parameter that Zend\Serializer supports.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addBuilder(Application $app)
    {
        $app['descriptor.builder.serializer'] = 'PhpSerialize';

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
    }

    /**
     * Adds the assembler factory and attaches the basic assemblers with key 'descriptor.builder.assembler.factory'.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addAssemblers(Application $app)
    {
        $app['descriptor.builder.assembler.factory'] = $app->share(
            function () {
                return new AssemblerFactory();
            }
        );

        $provider = $this;
        $app['descriptor.builder.assembler.factory'] = $app->share(
            $app->extend(
                'descriptor.builder.assembler.factory',
                function ($factory) use ($provider) {
                    return $provider->attachAssemblersToFactory($factory);
                }
            )
        );
    }

    /**
     * Adds the descriptor filtering mechanism and using key 'descriptor.filter'.
     *
     * Please note that filters can only be attached after the builder is instantiated because it is needed; so the
     * filters can be attached by extending 'descriptor.builder'.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addFilters(Application $app)
    {
        $app['descriptor.filter'] = $app->share(
            function () {
                return new Filter(new ClassFactory());
            }
        );
    }

    /**
     * Adds validators for the descriptors to the validator manager.
     *
     * @param Application $app
     *
     * @throws Exception\MissingDependencyException if the validator could not be found.
     *
     * @return void
     */
    protected function addValidators(Application $app)
    {
        if (!isset($app['validator'])) {
            throw new Exception\MissingDependencyException('The validator manager is missing');
        }

        $provider = $this;
        $app['validator'] = $app->share(
            $app->extend(
                'validator',
                function ($validatorManager) use ($provider) {
                    return $provider->attachValidators($validatorManager);
                }
            )
        );
    }
}
