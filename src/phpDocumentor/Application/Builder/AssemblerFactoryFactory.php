<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Builder;

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

final class AssemblerFactoryFactory
{
    public static function create(ExampleFinder $exampleFinder)
    {
        $factory = new AssemblerFactory();
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
        $factory->register($exampleMatcher, new ExampleAssembler($exampleFinder));
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

        $factory->registerFallback($tagFallbackMatcher, new GenericTagAssembler());

        return $factory;
    }
}
