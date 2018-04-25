<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Cilex\Application;
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
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
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
use Pimple\Container;
use Pimple\ServiceProviderInterface;


/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services needed to build the descriptors.
     *
     * @param Container $app An Application instance
     */
    public function register(Container $app)
    {

        // I would prefer to extend it but due to a circular reference will pimple fatal
        $this->attachFiltersToManager($app['descriptor.filter'], $app);

    }

    /**
     * Attaches filters to the manager.
     *
     * @return Filter
     */
    public function attachFiltersToManager(Filter $filterManager, Application $app)
    {
        $stripOnVisibility = new StripOnVisibility($app['descriptor.builder']);
        $filtersOnAllDescriptors = [
            new StripInternal($app['descriptor.builder']),
            new StripIgnore($app['descriptor.builder']),
        ];

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
}
