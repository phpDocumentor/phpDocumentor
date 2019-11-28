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
    private $projectDescriptorBuilder;
    private $namespaceUrlGenerator;
    private $fileUrlGenerator;
    private $packageUrlGenerator;
    private $classUrlGenerator;
    private $methodUrlGenerator;
    private $constantUrlGenerator;
    private $functionUrlGenerator;
    private $propertyUrlGenerator;
    private $fqsenUrlGenerator;

    public function __construct(
        ProjectDescriptorBuilder $projectDescriptorBuilder,
        UrlGenerator\Standard\NamespaceDescriptor $namespaceUrlGenerator,
        UrlGenerator\Standard\FileDescriptor $fileUrlGenerator,
        UrlGenerator\Standard\PackageDescriptor $packageUrlGenerator,
        UrlGenerator\Standard\ClassDescriptor $classUrlGenerator,
        UrlGenerator\Standard\MethodDescriptor $methodUrlGenerator,
        UrlGenerator\Standard\ConstantDescriptor $constantUrlGenerator,
        UrlGenerator\Standard\FunctionDescriptor $functionUrlGenerator,
        UrlGenerator\Standard\PropertyDescriptor $propertyUrlGenerator,
        UrlGenerator\Standard\FqsenDescriptor $fqsenUrlGenerator
    ) {
        $this->projectDescriptorBuilder = $projectDescriptorBuilder;
        $this->namespaceUrlGenerator = $namespaceUrlGenerator;
        $this->fileUrlGenerator = $fileUrlGenerator;
        $this->packageUrlGenerator = $packageUrlGenerator;
        $this->classUrlGenerator = $classUrlGenerator;
        $this->methodUrlGenerator = $methodUrlGenerator;
        $this->constantUrlGenerator = $constantUrlGenerator;
        $this->functionUrlGenerator = $functionUrlGenerator;
        $this->propertyUrlGenerator = $propertyUrlGenerator;
        $this->fqsenUrlGenerator = $fqsenUrlGenerator;

        parent::__construct();
    }

    /**
     * Configuration function to add routing rules to a router.
     */
    public function configure()
    {
        $projectDescriptorBuilder = $this->projectDescriptorBuilder;

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
        }, $this->fileUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PackageDescriptor;
        }, $this->packageUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof TraitDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof NamespaceDescriptor;
        }, $this->namespaceUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof InterfaceDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ClassDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ConstantDescriptor;
        }, $this->constantUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof MethodDescriptor;
        }, $this->methodUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof FunctionDescriptor;
        }, $this->functionUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PropertyDescriptor;
        }, $this->propertyUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof Fqsen;
        }, $this->fqsenUrlGenerator);

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
