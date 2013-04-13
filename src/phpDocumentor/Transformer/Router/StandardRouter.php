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
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;

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
        $namespaceGenerator = new UrlGenerator\Standard\NamespaceDescriptor();
        $packageGenerator = new UrlGenerator\Standard\PackageDescriptor();
        $classGenerator     = new UrlGenerator\Standard\ClassDescriptor();

        $this[] = new Rule(
            function ($node) {
                return ($node instanceof PackageDescriptor);
            },
            $packageGenerator
        );

        $this[] = new Rule(
            function ($node) {
                return ($node instanceof NamespaceDescriptor);
            },
            $namespaceGenerator
        );

        $this[] = new Rule(
            function ($node) {
                return ($node instanceof ClassDescriptor);
            },
            $classGenerator
        );

        $this[] = new Rule(
            function ($node) {
                return ($node instanceof InterfaceDescriptor);
            },
            $classGenerator
        );

        $this[] = new Rule(
            function ($node) {
                return ($node instanceof TraitDescriptor);
            },
            $classGenerator
        );

        // do not generate a file for every unknown type
        $this[] = new Rule(
            function ($node) {
                return true;
            },
            function () {
                return false;
            }
        );
    }
}
