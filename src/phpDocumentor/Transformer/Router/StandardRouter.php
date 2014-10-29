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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;

/**
 * The default router for phpDocumentor.
 */
class StandardRouter extends RouterAbstract
{
    /** @var ProjectDescriptorBuilder */
    private $projectDescriptorBuilder;

    /**
     * Initializes this router with a list of all elements.
     *
     * @param ProjectDescriptorBuilder $projectDescriptorBuilder
     */
    public function __construct(ProjectDescriptorBuilder $projectDescriptorBuilder)
    {
        $this->projectDescriptorBuilder = $projectDescriptorBuilder;

        parent::__construct();
    }

    /**
     * Configuration function to add routing rules to a router.
     *
     * @return void
     */
    public function configure()
    {
        $projectDescriptorBuilder = $this->projectDescriptorBuilder;

        $fileGenerator      = new UrlGenerator\Standard\FileDescriptor();
        $namespaceGenerator = new UrlGenerator\Standard\NamespaceDescriptor();
        $packageGenerator   = new UrlGenerator\Standard\PackageDescriptor();
        $classGenerator     = new UrlGenerator\Standard\ClassDescriptor();
        $methodGenerator    = new UrlGenerator\Standard\MethodDescriptor();
        $constantGenerator  = new UrlGenerator\Standard\ConstantDescriptor();
        $functionGenerator  = new UrlGenerator\Standard\FunctionDescriptor();
        $propertyGenerator  = new UrlGenerator\Standard\PropertyDescriptor();

        // Here we cheat! If a string element is passed to this rule then we try to transform it into a Descriptor
        // if the node is translated we do not let it match and instead fall through to one of the other rules.
        $stringRule = function (&$node) use ($projectDescriptorBuilder) {
            $elements = $projectDescriptorBuilder->getProjectDescriptor()->getIndexes()->get('elements');
            if (is_string($node) && isset($elements[$node])) {
                $node = $elements[$node];
            };

            return false;
        };

        // @codingStandardsIgnoreStart
        $this[] = new Rule($stringRule, function () { return false; });
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
