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
use phpDocumentor\Descriptor\Builder\Reflector\Tags\TypeCollectionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VersionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;
use phpDocumentor\Descriptor\Filter\ClassFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Descriptor\Validator\Ruleset;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Constraints as phpDocAssert;
use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;
use phpDocumentor\Reflection\DocBlock\Tag\DeprecatedTag;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
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
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;
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
     * @param Application $app An Application instance
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['parser.example.finder'] = new Example\Finder();

        $this->addCache($app);
        $this->addAssemblers($app);
        $this->addFilters($app);
        $this->addValidators($app);
        $this->addRulesets($app);
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
     * @param AssemblerFactory   $factory
     * @param \Cilex\Application $app
     *
     * @return AssemblerFactory
     */
    public function attachAssemblersToFactory(AssemblerFactory $factory, Application $app)
    {
        // @codingStandardsIgnoreStart because we limit the verbosity by making all closures single-line
        $fileMatcher      = function ($criteria) { return $criteria instanceof FileReflector; };
        $constantMatcher  = function ($criteria) {
            return $criteria instanceof ConstantReflector || $criteria instanceof ClassConstant;
        };
        $traitMatcher     = function ($criteria) { return $criteria instanceof TraitReflector; };
        $classMatcher     = function ($criteria) { return $criteria instanceof ClassReflector; };
        $interfaceMatcher = function ($criteria) { return $criteria instanceof InterfaceReflector; };
        $propertyMatcher  = function ($criteria) { return $criteria instanceof ClassReflector\PropertyReflector; };
        $methodMatcher    = function ($criteria) { return $criteria instanceof ClassReflector\MethodReflector; };
        $argumentMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector\ArgumentReflector; };
        $functionMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector; };

        $authorMatcher      = function ($criteria) { return $criteria instanceof AuthorTag; };
        $deprecatedMatcher  = function ($criteria) { return $criteria instanceof DeprecatedTag; };
        $exampleMatcher     = function ($criteria) { return $criteria instanceof ExampleTag; };
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

        $typeCollectionMatcher = function ($criteria) { return $criteria instanceof TypeCollection; };

        $tagFallbackMatcher = function ($criteria) { return $criteria instanceof Tag; };
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

        $factory->register($typeCollectionMatcher, new TypeCollectionAssembler());

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
            $filterManager->attach('phpDocumentor\Descriptor\ClassDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $filter);
        }

        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripOnVisibility);
        $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripOnVisibility);

        return $filterManager;
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
                $plugin = new SerializerPlugin();

                if (extension_loaded('igbinary')) {
                    $options = new PluginOptions();
                    $options->setSerializer('igbinary');

                    $plugin->setOptions($options);
                }

                $cache->addPlugin($plugin);

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
        if (extension_loaded('igbinary')) {
            $app['descriptor.builder.serializer'] = 'IgBinary';
        } else {
            $app['descriptor.builder.serializer'] = 'PhpSerialize';
        }

        $app['descriptor.builder'] = $app->share(
            function ($container) {
                $builder = new ProjectDescriptorBuilder(
                    $container['descriptor.builder.assembler.factory'],
                    $container['descriptor.filter'],
                    $container['validator'],
                    $container['validation.ruleset']
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
                function ($factory) use ($provider, $app) {
                    return $provider->attachAssemblersToFactory($factory, $app);
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
        $app['validator.collection'] = $app->share(
            function ($app) use ($provider) {
                $collection = new \phpDocumentor\Descriptor\Validator\Collection($app['validator']);

                $provider->attachValidators($collection);

                return $collection;
            }
        );
    }

    /**
     * Adds validators to check the Descriptors if they are enabled by the Ruleset.
     *
     * @param \phpDocumentor\Descriptor\Validator\Collection $collection
     *
     * @return void
     */
    private function attachValidators(\phpDocumentor\Descriptor\Validator\Collection $collection)
    {
        $collection['File.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'File.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['File.Package.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'File.Package.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSinglePackage($constraintOptions));
        };
        $collection['File.Subpackage.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'File.Subpackage.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage($constraintOptions));
        };
        $collection['File.Subpackage.CheckForPackage'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'File.Subpackage.CheckForPackage');
            $metaData->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage($constraintOptions));
        };

        $collection['Class.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Class.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['Class.Package.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Class.Package.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSinglePackage($constraintOptions));
        };
        $collection['Class.Subpackage.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Class.Subpackage.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage($constraintOptions));
        };
        $collection['Class.Subpackage.CheckForPackage'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Class.Subpackage.CheckForPackage');
            $metaData->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage($constraintOptions));
        };

        $collection['Interface.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Interface.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['Interface.Package.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Interface.Package.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSinglePackage($constraintOptions));
        };
        $collection['Interface.Subpackage.CheckForDuplicate'] =
            function (Validator $validator, ClassMetaData $metaData) {
                $constraintOptions = array('message' => 'Interface.Subpackage.CheckForDuplicate');
                $metaData->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage($constraintOptions));
            };
        $collection['Interface.Subpackage.CheckForPackage'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Interface.Subpackage.CheckForPackage');
            $metaData->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage($constraintOptions));
        };

        $collection['Trait.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Trait.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['Trait.Package.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Trait.Package.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSinglePackage($constraintOptions));
        };
        $collection['Trait.Subpackage.CheckForDuplicate'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Trait.Subpackage.CheckForDuplicate');
            $metaData->addConstraint(new phpDocAssert\Classes\HasSingleSubpackage($constraintOptions));
        };
        $collection['Trait.Subpackage.CheckForPackage'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Trait.Subpackage.CheckForPackage');
            $metaData->addConstraint(new phpDocAssert\Classes\HasPackageWithSubpackage($constraintOptions));
        };

        $collection['Function.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Function.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['Function.Return.NotAnIdeDefault'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Function.Return.NotAnIdeDefault');
            $metaData->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault($constraintOptions));
        };
        $collection['Function.Param.NotAnIdeDefault'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Function.Param.NotAnIdeDefault');
            $metaData->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault($constraintOptions));
        };
        $collection['Function.Param.ArgumentInDocBlock'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Function.Param.ArgumentInDocBlock');
            $metaData->addConstraint(new phpDocAssert\Functions\IsArgumentInDocBlock($constraintOptions));
        };

        $collection['Method.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Method.Summary.Missing');
            $metaData->addPropertyConstraint('summary', new Assert\NotBlank($constraintOptions));
        };
        $collection['Method.Return.NotAnIdeDefault'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Method.Return.NotAnIdeDefault');
            $metaData->addConstraint(new phpDocAssert\Functions\IsReturnTypeNotAnIdeDefault($constraintOptions));
        };
        $collection['Method.Param.NotAnIdeDefault'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Method.Param.NotAnIdeDefault');
            $metaData->addConstraint(new phpDocAssert\Functions\IsParamTypeNotAnIdeDefault($constraintOptions));
        };
        $collection['Method.Param.ArgumentInDocBlock'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Method.Param.ArgumentInDocBlock');
            $metaData->addConstraint(new phpDocAssert\Functions\IsArgumentInDocBlock($constraintOptions));
        };

        $collection['Property.Summary.Missing'] = function (Validator $validator, ClassMetaData $metaData) {
            $constraintOptions = array('message' => 'Property.Summary.Missing');
            $metaData->addConstraint(new phpDocAssert\Property\HasSummary($constraintOptions));
        };
    }

    /**
     * @param Application $app
     */
    private function addRulesets(Application $app)
    {
        $app['validation.rulesets'] = array(
            'Default' => new Ruleset\DefaultRuleset()
        );
        $app['validation.ruleset'] = $app['validation.rulesets']['Default'];

        // TODO: detect if the configuration or command line has a different rule set

        /** @var Ruleset $ruleset */
        $ruleset = $app['validation.ruleset'];
        $ruleset->enableValidations($app['validator.collection']);
    }
}
