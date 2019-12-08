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

namespace phpDocumentor\Transformer\Router;

use ArrayObject;
use InvalidArgumentException;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * The default for phpDocumentor.
 */
class Router extends ArrayObject
{
    /** @var UrlGenerator\FqsenDescriptor */
    private $fqsenUrlGenerator;

    /** @var QualifiedNameToUrlConverter */
    private $converter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UrlGenerator\FqsenDescriptor $fqsenUrlGenerator,
        QualifiedNameToUrlConverter $converter,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->fqsenUrlGenerator = $fqsenUrlGenerator;
        $this->converter = $converter;
        $this->urlGenerator = $urlGenerator;

        parent::__construct();
    }

    public function generate($node) : ?string
    {
        if ($node instanceof FileDescriptor) {
            return $this->generateUrlForDescriptor('file', $node->getPath());
        }
        if ($node instanceof PackageDescriptor) {
            return $this->generateUrlForDescriptor(
                'package',
                (string) $node->getFullyQualifiedStructuralElementName()
            );
        }
        if ($node instanceof NamespaceDescriptor) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getFullyQualifiedStructuralElementName()
            );
        }
        if ($node instanceof ClassDescriptor
            || $node instanceof InterfaceDescriptor
            || $node instanceof TraitDescriptor
        ) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getFullyQualifiedStructuralElementName()
            );
        }
        if ($node instanceof ConstantDescriptor
            && ($node->getParent() instanceof FileDescriptor || !$node->getParent())) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getNamespace(),
                'constant_' . $node->getName()
            );
        }
        if ($node instanceof ConstantDescriptor
            && !($node->getParent() instanceof FileDescriptor || !$node->getParent())) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'constant_' . $node->getName()
            );
        }
        if ($node instanceof MethodDescriptor) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'method_' . $node->getName()
            );
        }
        if ($node instanceof FunctionDescriptor) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getNamespace(),
                'function_' . $node->getName()
            );
        }
        if ($node instanceof PropertyDescriptor) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'property_' . $node->getName()
            );
        }
        if ($node instanceof Fqsen) {
            return ($this->fqsenUrlGenerator)($node);
        }

        // if this is a link to an external page; return that URL
        if ($node instanceof Url) {
            return (string) $node;
        }

        // We could not match the node to any known routable thing
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
                $fqsen = $this->removeFileExtensionFromPath($fqsen);
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

    /**
     * Removes the file extension from the provided path.
     */
    private function removeFileExtensionFromPath(string $path) : string
    {
        if (strrpos($path, '.') !== false) {
            $path = substr($path, 0, strrpos($path, '.'));
        }

        return $path;
    }
}
