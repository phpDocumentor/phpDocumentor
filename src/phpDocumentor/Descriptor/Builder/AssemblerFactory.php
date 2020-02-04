<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder;

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
use phpDocumentor\Descriptor\Builder\Reflector\Tags\InvalidTagAssembler;
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
use function array_merge;

/**
 * Attempts to retrieve an Assembler for the provided criteria.
 */
class AssemblerFactory
{
    /** @var AssemblerMatcher[] */
    protected $assemblers = [];

    /** @var AssemblerMatcher[] */
    protected $fallbackAssemblers = [];

    /**
     * Registers an assembler instance to this factory.
     *
     * @param callable $matcher A callback function accepting the criteria as only parameter and which must
     *     return a boolean.
     * @param AssemblerInterface $assembler An instance of the Assembler that will be returned if the callback returns
     *     true with the provided criteria.
     */
    public function register(callable $matcher, AssemblerInterface $assembler) : void
    {
        $this->assemblers[] = new AssemblerMatcher($matcher, $assembler);
    }

    /**
     * Registers an assembler instance to this factory that is to be executed after all other assemblers have been
     * checked.
     *
     * @param callable $matcher A callback function accepting the criteria as only parameter and which must
     *     return a boolean.
     * @param AssemblerInterface $assembler An instance of the Assembler that will be returned if the callback returns
     *     true with the provided criteria.
     */
    public function registerFallback(callable $matcher, AssemblerInterface $assembler) : void
    {
        $this->fallbackAssemblers[] = new AssemblerMatcher($matcher, $assembler);
    }

    /**
     * Retrieves a matching Assembler based on the provided criteria or null if none was found.
     *
     * @param mixed $criteria
     */
    public function get($criteria) : ?AssemblerInterface
    {
        /** @var AssemblerMatcher $candidate */
        foreach (array_merge($this->assemblers, $this->fallbackAssemblers) as $candidate) {
            if ($candidate->match($criteria) === true) {
                return $candidate->getAssembler();
            }
        }

        return null;
    }
}
