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
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\EnumCaseInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Reflection\Fqsen;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

use function strrpos;
use function substr;

class Router
{
    private readonly UrlGeneratorInterface $urlGenerator;
    private readonly SluggerInterface $slugger;
    private readonly ClassBasedFqsenUrlGenerator $fqsenUrlGenerator;

    public function __construct()
    {
        $routes = new RouteCollection();
        $routes->add('document', new Route('/{name}.html', [], ['name' => '.+']));
        $routes->add('file', new Route('/files/{name}.html'));
        $routes->add('package', new Route('/packages/{name}.html'));
        $routes->add('namespace', new Route('/namespaces/{name}.html'));
        $routes->add('class', new Route('/classes/{name}.html'));

        $this->urlGenerator = new UrlGenerator(
            $routes,
            new RequestContext(),
        );

        $this->slugger = new AsciiSlugger();
        $this->fqsenUrlGenerator = new ClassBasedFqsenUrlGenerator($this->urlGenerator, $this->slugger);
    }

    /** @param ElementInterface|Descriptor|Fqsen|UriInterface $node */
    public function generate(object $node): string
    {
        if ($node instanceof DocumentDescriptor) {
            return $this->urlGenerator->generate(
                'document',
                ['name' => $node->getFile()],
            );
        }

        if ($node instanceof FileInterface) {
            return $this->generateUrlForDescriptor('file', $node->getPath());
        }

        if ($node instanceof PackageInterface) {
            return $this->generateUrlForDescriptor(
                'package',
                (string) $node->getFullyQualifiedStructuralElementName(),
            );
        }

        if ($node instanceof NamespaceInterface) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getFullyQualifiedStructuralElementName(),
            );
        }

        if (
            $node instanceof ClassInterface
            || $node instanceof InterfaceInterface
            || $node instanceof TraitInterface
            || $node instanceof EnumInterface
        ) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getFullyQualifiedStructuralElementName(),
            );
        }

        if ($node instanceof EnumCaseInterface && $node->getParent() instanceof EnumInterface) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'enumcase_' . $node->getName(),
            );
        }

        if ($node instanceof ConstantInterface && $node->getParent() === null) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getNamespace(),
                'constant_' . $node->getName(),
            );
        }

        if ($node instanceof ConstantInterface && $node->getParent() !== null) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'constant_' . $node->getName(),
            );
        }

        if ($node instanceof MethodInterface) {
            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'method_' . $node->getName(),
            );
        }

        if ($node instanceof FunctionInterface) {
            return $this->generateUrlForDescriptor(
                'namespace',
                (string) $node->getNamespace(),
                'function_' . $node->getName(),
            );
        }

        if ($node instanceof PropertyInterface) {
            if ($node->getParent() === null) {
                return '';
            }

            return $this->generateUrlForDescriptor(
                'class',
                (string) $node->getParent()->getFullyQualifiedStructuralElementName(),
                'property_' . $node->getName(),
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
            ['name' => $name, '_fragment' => $fragment],
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
