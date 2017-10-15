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
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FileAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\NamespaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\AuthorAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\DeprecatedAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;
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
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints as phpDocAssert;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use phpDocumentor\Reflection\DocBlock\Tags\Deprecated;
use phpDocumentor\Reflection\DocBlock\Tags\Example;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\DocBlock\Tags\Since;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlock\Tags\Version;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Namespace_;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Trait_;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;
use Zend\Cache\Storage\Plugin\PluginOptions;

/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services needed to build the descriptors.
     *
     * @param Container $app An Application instance
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['parser.example.finder'] = new ExampleFinder();

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
     * @param \Cilex\Application $app
     *
     * @return AssemblerFactory
     */
    public function attachAssemblersToFactory(AssemblerFactory $factory, Application $app)
    {
        // @codingStandardsIgnoreStart because we limit the verbosity by making all closures single-line
        $fileMatcher = function ($criteria) {
            return $criteria instanceof File;
        };
        $constantMatcher = function ($criteria) {
            return $criteria instanceof Constant; // || $criteria instanceof ClassConstant;
        };
        $traitMatcher = function ($criteria) {
            return $criteria instanceof Trait_;
        };
        $classMatcher = function ($criteria) {
            return $criteria instanceof Class_;
        };
        $interfaceMatcher = function ($criteria) {
            return $criteria instanceof Interface_;
        };
        $propertyMatcher = function ($criteria) {
            return $criteria instanceof Property;
        };
        $methodMatcher = function ($criteria) {
            return $criteria instanceof Method;
        };
        $argumentMatcher = function ($criteria) {
            return $criteria instanceof Argument;
        };
        $functionMatcher = function ($criteria) {
            return $criteria instanceof Function_;
        };
        $namespaceMatcher = function ($criteria) {
            return $criteria instanceof Namespace_;
        };

        $authorMatcher = function ($criteria) {
            return $criteria instanceof Author;
        };
        $deprecatedMatcher = function ($criteria) {
            return $criteria instanceof Deprecated;
        };
        $exampleMatcher = function ($criteria) {
            return $criteria instanceof Example;
        };
        $linkMatcher = function ($criteria) {
            return $criteria instanceof Link;
        };
        $methodTagMatcher = function ($criteria) {
            return $criteria instanceof Tags\Method;
        };
        $propertyTagMatcher = function ($criteria) {
            return $criteria instanceof Tags\Property;
        };
        $paramMatcher = function ($criteria) {
            return $criteria instanceof Param;
        };
        $throwsMatcher = function ($criteria) {
            return $criteria instanceof Throws;
        };
        $returnMatcher = function ($criteria) {
            return $criteria instanceof Return_;
        };
        $usesMatcher = function ($criteria) {
            return $criteria instanceof Uses;
        };
        $seeMatcher = function ($criteria) {
            return $criteria instanceof See;
        };
        $sinceMatcher = function ($criteria) {
            return $criteria instanceof Since;
        };
        $varMatcher = function ($criteria) {
            return $criteria instanceof Var_;
        };
        $versionMatcher = function ($criteria) {
            return $criteria instanceof Version;
        };

        //$typeCollectionMatcher = function ($criteria) { return $criteria instanceof TypeCollection; };

        $tagFallbackMatcher = function ($criteria) {
            return $criteria instanceof Tag;
        };
        // @codingStandardsIgnoreEnd

        $argumentAssembler = new ArgumentAssembler();
        $factory->register($fileMatcher, new FileAssembler());
        $factory->register($constantMatcher, new ConstantAssembler());
        $factory->register($traitMatcher, new TraitAssembler());
        $factory->register($classMatcher, new ClassAssembler());
        $factory->register($interfaceMatcher, new InterfaceAssembler());
        $factory->register($propertyMatcher, new PropertyAssembler());
        $factory->register($argumentMatcher, $argumentAssembler);
        $factory->register($methodMatcher, new MethodAssembler($argumentAssembler));
        $factory->register($functionMatcher, new FunctionAssembler($argumentAssembler));
        $factory->register($namespaceMatcher, new NamespaceAssembler());

        $factory->register($authorMatcher, new AuthorAssembler());
        $factory->register($deprecatedMatcher, new DeprecatedAssembler());
        $factory->register($exampleMatcher, new ExampleAssembler($app['parser.example.finder']));
        $factory->register($linkMatcher, new LinkAssembler());
        $factory->register($methodTagMatcher, new MethodTagAssembler());
        $factory->register($propertyTagMatcher, new PropertyTagAssembler());
        $factory->register($varMatcher, new VarAssembler());
        $factory->register($paramMatcher, new ParamAssembler());
        $factory->register($throwsMatcher, new ThrowsAssembler());
        $factory->register($returnMatcher, new ReturnAssembler());
        $factory->register($usesMatcher, new UsesAssembler());
        $factory->register($seeMatcher, new SeeAssembler());
        $factory->register($sinceMatcher, new SinceAssembler());
        $factory->register($versionMatcher, new VersionAssembler());

//        $factory->register($typeCollectionMatcher, new TypeCollectionAssembler());

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
        $stripOnVisibility = new StripOnVisibility($app['descriptor.builder']);
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

        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripOnVisibility);
        $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripOnVisibility);

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
        $fileMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\FileDescriptor');
        /** @var ClassMetadata $constantMetadata */
        $constantMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\ConstantDescriptor');
        /** @var ClassMetadata $functionMetadata */
        $functionMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\FunctionDescriptor');
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\ClassDescriptor');
        /** @var ClassMetadata $interfaceMetadata */
        $interfaceMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\InterfaceDescriptor');
        /** @var ClassMetadata $traitMetadata */
        $traitMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\TraitDescriptor');
        /** @var ClassMetadata $propertyMetadata */
        $propertyMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\PropertyDescriptor');
        /** @var ClassMetadata $methodMetadata */
        $methodMetadata = $validator->getMetadataFor('phpDocumentor\Descriptor\MethodDescriptor');

        $fileMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50000')));
        $classMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50005')));
        $propertyMetadata->addConstraint(new phpDocAssert\Property\HasSummary());
        $methodMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50008')));
        $interfaceMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50009')));
        $traitMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50010')));
        $functionMetadata->addPropertyConstraint('summary', new Assert\NotBlank(array('message' => 'PPC:ERR-50011')));

        $functionMetadata->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault());

        $functionMetadata->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault());
        $functionMetadata->addConstraint(new phpDocAssert\Functions\AreAllArgumentsValid());
        $methodMetadata->addConstraint(new phpDocAssert\Functions\AreAllArgumentsValid());

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
        $app['descriptor.cache'] = function () {
            $cache = new Filesystem();
            $cache->setOptions(
                array(
                    'namespace' => 'phpdoc-cache',
                    'cache_dir' => sys_get_temp_dir(),
                )
            );
            $plugin = new SerializerPlugin();

            if (extension_loaded('igbinary')) {
                $options = new PluginOptions();
                $options->setSerializer('igbinary');

                $plugin->setOptions($options);
            }

            $cache->addPlugin($plugin);

            return $cache;
        };
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
        if (extension_loaded('igbinary')) {
            $app['descriptor.builder.serializer'] = 'IgBinary';
        } else {
            $app['descriptor.builder.serializer'] = 'PhpSerialize';
        }

        $app['descriptor.builder'] = function ($container) {
            $builder = new ProjectDescriptorBuilder(
                $container['descriptor.builder.assembler.factory'],
                $container['descriptor.filter'],
                $container['validator']
            );
            $builder->setTranslator($container['translator']);

            return $builder;
        };
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
        $app['descriptor.builder.assembler.factory'] = function () {
            return new AssemblerFactory();
        };

        $provider = $this;
        $app['descriptor.builder.assembler.factory'] = $app->extend(
            'descriptor.builder.assembler.factory',
            function ($factory) use ($provider, $app) {
                return $provider->attachAssemblersToFactory($factory, $app);
            }
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
        $app['descriptor.filter'] = function () {
            return new Filter(new ClassFactory());
        };
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
        $app['validator'] = $app->extend(
            'validator',
            function ($validatorManager) use ($provider) {
                return $provider->attachValidators($validatorManager);
            }
        );
    }
}
