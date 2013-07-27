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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;

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
        $packageGenerator   = new UrlGenerator\Standard\PackageDescriptor();
        $classGenerator     = new UrlGenerator\Standard\ClassDescriptor();
        $methodGenerator    = new UrlGenerator\Standard\MethodDescriptor();
        $constantGenerator  = new UrlGenerator\Standard\ConstantDescriptor();
        $functionGenerator  = new UrlGenerator\Standard\FunctionDescriptor();
        $propertyGenerator  = new UrlGenerator\Standard\PropertyDescriptor();

        // @codingStandardsIgnoreStart
        $this[] = new Rule(function ($node) { return ($node instanceof FileDescriptor); }, $fileGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof PackageDescriptor); }, $packageGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof TraitDescriptor); }, $classGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof NamespaceDescriptor); }, $namespaceGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof InterfaceDescriptor); }, $classGenerator );
        $this[] = new Rule(function ($node) { return ($node instanceof ClassDescriptor); }, $classGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof ConstantDescriptor); }, $constantGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof MethodDescriptor); }, $methodGenerator);
        $this[] = new Rule(function ($node) { return ($node instanceof FunctionDescriptor); }, $functionGenerator);
        $this[] = new Rule( function ($node) { return ($node instanceof PropertyDescriptor); }, $propertyGenerator);

        // do not generate a file for every unknown type
        $this[] = new Rule(function ($node) { return true; }, function () { return false; });
        // @codingStandardsIgnoreEnd
    }
}
