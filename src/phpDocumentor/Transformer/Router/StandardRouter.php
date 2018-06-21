<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

/**
 * The default router for phpDocumentor.
 */
class StandardRouter extends RouterAbstract
{
    /** @var ProjectDescriptorBuilder */
    private $projectDescriptorBuilder;

    /**
     * Initializes this router with a list of all elements.
     */
    public function __construct(ProjectDescriptorBuilder $projectDescriptorBuilder)
    {
        $this->projectDescriptorBuilder = $projectDescriptorBuilder;

        parent::__construct();
    }

    /**
     * Configuration function to add routing rules to a router.
     */
    public function configure()
    {
        $projectDescriptorBuilder = $this->projectDescriptorBuilder;

        $fileGenerator = new UrlGenerator\Standard\FileDescriptor();
        $namespaceGenerator = new UrlGenerator\Standard\NamespaceDescriptor();
        $packageGenerator = new UrlGenerator\Standard\PackageDescriptor();
        $classGenerator = new UrlGenerator\Standard\ClassDescriptor();
        $methodGenerator = new UrlGenerator\Standard\MethodDescriptor();
        $constantGenerator = new UrlGenerator\Standard\ConstantDescriptor();
        $functionGenerator = new UrlGenerator\Standard\FunctionDescriptor();
        $propertyGenerator = new UrlGenerator\Standard\PropertyDescriptor();
        $fqsenGenerator = new UrlGenerator\Standard\FqsenDescriptor();

        // Here we cheat! If a string element is passed to this rule then we try to transform it into a Descriptor
        // if the node is translated we do not let it match and instead fall through to one of the other rules.
        $stringRule = function (&$node) use ($projectDescriptorBuilder) {
            $elements = $projectDescriptorBuilder->getProjectDescriptor()->getIndexes()->get('elements');
            if (is_string($node) && isset($elements[$node])) {
                $node = $elements[$node];
            }

            return false;
        };

        // @codingStandardsIgnoreStart
        $this[] = new Rule($stringRule, function () {
            return false;
        });
        $this[] = new Rule(function ($node) {
            return $node instanceof FileDescriptor;
        }, $fileGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PackageDescriptor;
        }, $packageGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof TraitDescriptor;
        }, $classGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof NamespaceDescriptor;
        }, $namespaceGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof InterfaceDescriptor;
        }, $classGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ClassDescriptor;
        }, $classGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ConstantDescriptor;
        }, $constantGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof MethodDescriptor;
        }, $methodGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof FunctionDescriptor;
        }, $functionGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PropertyDescriptor;
        }, $propertyGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof Fqsen;
        }, $fqsenGenerator);

        // if this is a link to an external page; return that URL
        $this[] = new Rule(
            function ($node) {
                return $node instanceof Url;
            },
            function ($node) {
                return (string) $node;
            }
        );

        // do not generate a file for every unknown type
        $this[] = new Rule(function () {
            return true;
        }, function () {
            return false;
        });
        // @codingStandardsIgnoreEnd
    }
}
