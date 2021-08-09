<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use League\Uri\Contracts\UriInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

use function strrpos;
use function substr;

class Router
{
    /** @var ClassBasedFqsenUrlGenerator */
    private $fqsenUrlGenerator;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var SluggerInterface */
    private $slugger;

    public function __construct(
        ClassBasedFqsenUrlGenerator $fqsenUrlGenerator,
        UrlGeneratorInterface $urlGenerator,
        SluggerInterface $slugger
    ) {
        $this->fqsenUrlGenerator = $fqsenUrlGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->slugger = $slugger;
    }

    /**
     * @param Descriptor|Fqsen|UriInterface $node
     */
    public function generate(object $node): string
    {
        if ($node instanceof DocumentDescriptor) {
            return $this->urlGenerator->generate(
                'document',
                ['name' => $node->getFile()]
            );
        }

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

        if (
            $node instanceof ClassDescriptor
            || $node instanceof InterfaceDescriptor
            || $node instanceof TraitDescriptor
        ) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getFullyQualifiedStructuralElementName()
            );
        }

        if ($node instanceof ConstantDescriptor && $node->getParent() === null) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getNamespace(),
                'constant_' . $node->getName()
            );
        }

        if ($node instanceof ConstantDescriptor && $node->getParent() !== null) {
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
            if ($node->getParent() === null) {
                return '';
            }

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
        if ($node instanceof UriInterface) {
            return (string) $node;
        }

        // We could not match the node to any known routable thing
        return '';
    }

    private function generateUrlForDescriptor(string $type, string $fqsen, string $fragment = ''): string
    {
        $name = $this->slugifyNameBasedOnType($type, $fqsen);

        return $this->urlGenerator->generate(
            $type,
            ['name' => $name, '_fragment' => $fragment]
        );
    }

    private function slugifyNameBasedOnType(string $type, string $name): string
    {
        if ($type === 'file') {
            return $this->slugger->slug($this->removeFileExtensionFromPath($name))->lower()->toString();
        }

        $default = $type === 'class' ? '' : 'default';

        $slug = $this->slugger->slug($name);
        if ($type === 'namespace') {
            $slug = $slug->lower();
        }

        return $slug->toString() ?: $default;
    }

    /**
     * Removes the file extension from the provided path.
     */
    private function removeFileExtensionFromPath(string $path): string
    {
        if (strrpos($path, '.') !== false) {
            $path = substr($path, 0, strrpos($path, '.'));
        }

        return $path;
    }
}
