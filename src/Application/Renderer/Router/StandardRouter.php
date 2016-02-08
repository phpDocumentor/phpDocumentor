<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Router;

use phpDocumentor\DomainModel\Renderer\Router\Rule;
use phpDocumentor\Reflection\Php\Class_ as ClassDescriptor;
use phpDocumentor\Reflection\Php\Constant as ConstantDescriptor;
use phpDocumentor\Reflection\Php\Function_ as FunctionDescriptor;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceDescriptor;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;
use phpDocumentor\Reflection\Php\Namespace_ as NamespaceDescriptor;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\Trait_ as TraitDescriptor;
use phpDocumentor\Reflection\Php\File as FileDescriptor;
use \phpDocumentor\DomainModel\Renderer\Router\phpDocumentor;
use \phpDocumentor\DomainModel\Renderer\Router\RouterAbstract;

/**
 * The default router for phpDocumentor.
 */
class StandardRouter extends RouterAbstract
{
    /**
     * Configuration function to add routing rules to a router.
     *
     * @return void
     */
    public function configure()
    {
        $fileGenerator      = new UrlGenerator\Standard\FileDescriptor();
        $namespaceGenerator = new UrlGenerator\Standard\NamespaceDescriptor();
        $classGenerator     = new UrlGenerator\Standard\ClassDescriptor();
        $methodGenerator    = new UrlGenerator\Standard\MethodDescriptor();
        $constantGenerator  = new UrlGenerator\Standard\ConstantDescriptor();
        $functionGenerator  = new UrlGenerator\Standard\FunctionDescriptor();
        $propertyGenerator  = new UrlGenerator\Standard\PropertyDescriptor();

        $stringRule = function ($node) {
            return false;
        };

        // @codingStandardsIgnoreStart
        $this[] = new Rule($stringRule, function () { return false; });
        $this[] = new Rule(function ($node) { return ($node instanceof FileDescriptor); }, $fileGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof TraitDescriptor); }, $classGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof NamespaceDescriptor); }, $namespaceGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof InterfaceDescriptor); }, $classGenerator );
        $this[] = new Rule(function ($node) { return ($node instanceof ClassDescriptor); }, $classGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof ConstantDescriptor); }, $constantGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof MethodDescriptor); }, $methodGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof FunctionDescriptor); }, $functionGenerator);
        $this[] = new Rule( function ($node) { return ($node instanceof PropertyDescriptor); }, $propertyGenerator);

        // if this is a link to an external page; return that URL
        $this[] = new Rule(
            function ($node) {
                return is_string($node) && (substr($node, 0, 7) == 'http://' || substr($node, 0, 7) == 'https://');
            },
            function ($node) { return $node; }
        );

        // do not generate a file for every unknown type
        $this[] = new Rule(function () { return true; }, function () { return false; });
        // @codingStandardsIgnoreEnd
    }
}
