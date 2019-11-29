<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use ArrayObject;
use InvalidArgumentException;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
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
use phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function is_string;

/**
 * The default for phpDocumentor.
 */
class Router extends ArrayObject
{
    private $projectDescriptorBuilder;
    private $fqsenUrlGenerator;
    private $converter;
    private $urlGenerator;

    public function __construct(
        ProjectDescriptorBuilder $projectDescriptorBuilder,
        UrlGenerator\FqsenDescriptor $fqsenUrlGenerator,
        QualifiedNameToUrlConverter $converter,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->projectDescriptorBuilder = $projectDescriptorBuilder;
        $this->fqsenUrlGenerator        = $fqsenUrlGenerator;
        $this->converter                = $converter;
        $this->urlGenerator             = $urlGenerator;

        parent::__construct();
        $this->configure();
    }

    /**
     * Configuration function to add routing rules to a router.
     */
    public function configure() : void
    {
        // @codingStandardsIgnoreStart
        $this[] = new Rule(
            function ($node) {
                return $node instanceof FileDescriptor;
            },
            function (FileDescriptor $node): string {
                return $this->generateUrlForDescriptor('file', $node->getPath());
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof PackageDescriptor;
            },
            function (PackageDescriptor $node): string {
                return $this->generateUrlForDescriptor('package', (string) $node->getFullyQualifiedStructuralElementName());
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof NamespaceDescriptor;
            },
            function (NamespaceDescriptor $node): string {
                return $this->generateUrlForDescriptor('namespace', (string) $node->getFullyQualifiedStructuralElementName());
            }
        );
        $this[] = new Rule(
            function ($node): bool {
                return $node instanceof ClassDescriptor || $node instanceof InterfaceDescriptor || $node instanceof TraitDescriptor;
            },
            function (DescriptorAbstract $node): string {
                return $this->generateUrlForDescriptor('class', (string) $node->getFullyQualifiedStructuralElementName());
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof ConstantDescriptor
                    && ($node->getParent() instanceof FileDescriptor || !$node->getParent());
            },
            function (ConstantDescriptor $node): string {
                return $this->generateUrlForDescriptor(
                    'namespace',
                    (string) $node->getNamespace(),
                    'constant_' . $node->getName()
                );
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof ConstantDescriptor
                    && !($node->getParent() instanceof FileDescriptor || !$node->getParent());
            },
            function (ConstantDescriptor $node): string {
                return $this->generateUrlForDescriptor(
                    'class',
                    (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                    'constant_' . $node->getName()
                );
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof MethodDescriptor;
            },
            function (MethodDescriptor $node): string {
                return $this->generateUrlForDescriptor(
                    'class',
                    (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                    'method_' . $node->getName()
                );
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof FunctionDescriptor;
            },
            function (FunctionDescriptor $node): string {
                return $this->generateUrlForDescriptor(
                    'namespace',
                    (string) $node->getNamespace(),
                    'function_' . $node->getName()
                );
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof PropertyDescriptor;
            },
            function (PropertyDescriptor $node): string {
                return $this->generateUrlForDescriptor(
                    'class',
                    (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                    'property_' . $node->getName()
                );
            }
        );
        $this[] = new Rule(
            function ($node) {
                return $node instanceof Fqsen;
            }, $this->fqsenUrlGenerator
        );

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
        $this[] = new Rule(
            function () {
                return true;
            },
            function () {
                return false;
            }
        );
        // @codingStandardsIgnoreEnd
    }

    public function generate($node) : ?string
    {
        $rule = $this->match($node);
        if (!$rule) {
            return null;
        }

        return $rule->generate($node) ?: null;
    }

    /**
     * Tries to match the provided node with one of the rules in this router.
     *
     * @param string|DescriptorAbstract $node
     */
    private function match($node) : ?Rule
    {
        /** @var Rule $rule */
        foreach ($this as $rule) {
            if ($rule->match($node)) {
                return $rule;
            }
        }

        return null;
    }

    private function generateUrlForDescriptor(string $type, string $fqsen, string $fragment = '') : string
    {
        switch ($type) {
            case 'namespace':
                $name = $this->converter->fromNamespace($fqsen);
                break;
            case 'class':
                $name = $this->converter->fromClass($fqsen);
                break;
            case 'package':
                $name = $this->converter->fromPackage($fqsen);
                break;
            case 'file':
                $name = $this->converter->fromFile($fqsen);
                break;
            default:
                throw new InvalidArgumentException('Unknown url type');
        }

        return $this->urlGenerator->generate(
            $type,
            ['name' => $name, '_fragment' => $fragment]
        );
    }
}
