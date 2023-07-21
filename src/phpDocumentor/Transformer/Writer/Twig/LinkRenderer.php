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

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\AbstractListAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\ArrayOfTypeAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\CallableAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\HtmlFormatter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\IterableAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\LinkAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\NullableAdapter;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\UrlGenerator;
use RuntimeException;

/**
 * Renders an HTML anchor pointing to the location of the provided element.
 */
class LinkRenderer implements LinkRendererInterface
{
    final public const PRESENTATION_NONE = '';
    final public const PRESENTATION_NORMAL = 'normal';
    final public const PRESENTATION_URL = 'url';
    final public const PRESENTATION_CLASS_SHORT = 'class:short';
    final public const PRESENTATION_FILE_SHORT = 'file:short';

    private string $destination = '';
    private ProjectDescriptor $project;
    private DocumentationSetDescriptor $documentationSet;

    /** @var LinkRendererInterface[] */
    private array $adapters;

    public function __construct(private Router $router, private HtmlFormatter $htmlFormatter)
    {
        $this->adapters = $this->createAdapters();
    }

    public function __clone()
    {
        // recreate adapters because they need the current instance
        $this->adapters = $this->createAdapters();
    }

    /** @deprecated will be removed once getProject is removed */
    public function withProject(ProjectDescriptor $projectDescriptor): self
    {
        $result = clone $this;
        $result->project = $projectDescriptor;

        return $result;
    }

    public function forDocumentationSet(DocumentationSetDescriptor $documentationSet): self
    {
        $result = clone $this;
        $result->documentationSet = $documentationSet;

        return $result;
    }

    /** @deprecated use {@see getDocumentationSet()} */
    public function getProject(): ProjectDescriptor
    {
        return $this->project;
    }

    public function getDocumentationSet(): DocumentationSetDescriptor
    {
        return $this->documentationSet;
    }

    /**
     * Sets the destination directory relative to the Project's Root.
     *
     * The destination is the target directory containing the resulting
     * file. This destination is relative to the Project's root and can
     * be used for the calculation of nesting depths, etc.
     *
     * For this specific extension the destination is provided in the
     * Twig writer itself.
     */
    public function withDestination(string $destination): self
    {
        $result = clone $this;
        $result->destination = $destination;

        return $result;
    }

    /**
     * Returns the target directory relative to the Project's Root.
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($value): bool
    {
        return true;
    }

    /**
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @return string|list<string>
     */
    public function render($value, string $presentation)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($value) === false) {
                continue;
            }

            return $adapter->render($value, $presentation);
        }

        throw new RuntimeException(
            'The last adapter should have been a cap that accepts anything, this should not happen',
        );
    }

    /** @return array<array-key, LinkRendererInterface> */
    private function createAdapters(): array
    {
        // TODO: Because the renderer uses an immutable pattern to change itself; the $this references
        // below get lost. For now we solved it in __clone(), but as soon as we move these dependencies
        // to the container that won't work anymore..
        return [
            new ArrayOfTypeAdapter(),
            new NullableAdapter($this),
            new AbstractListAdapter($this),
            new IterableAdapter($this),
            new CallableAdapter($this),
            new LinkAdapter(
                $this,
                new UrlGenerator($this, $this->router),
                $this->htmlFormatter,
            ),
        ];
    }
}
