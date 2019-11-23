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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Transformer\Router\Queue;

/**
 * This step in the compilation process iterates through all elements and scans their descriptions for an inline `@see`
 * or `@link` tag and resolves them to a markdown link.
 */
class ResolveInlineLinkAndSeeTags implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9002;

    const REGEX_INLINE_LINK_OR_SEE_TAG = '/\{\@(see|link)[\ ]+([^\}]+)\}/';

    /** @var Queue */
    private $router;

    /** @var DescriptorAbstract */
    private $descriptor;

    /** @var Collection */
    private $elementCollection;

    /**
     * Registers the router queue with this pass.
     */
    public function __construct(Queue $router)
    {
        $this->router = $router;
    }

    public function getDescription(): string
    {
        return 'Resolve @link and @see tags in descriptions';
    }

    /**
     * Iterates through each element in the project and replaces its inline @see and @link tag with a markdown
     * representation.
     */
    public function execute(ProjectDescriptor $project): void
    {
        /** @var Collection|DescriptorAbstract[] $elementCollection */
        $this->elementCollection = $project->getIndexes()->get('elements');

        foreach ($this->elementCollection as $descriptor) {
            $this->resolveSeeAndLinkTags($descriptor);
        }
    }

    /**
     * Resolves all @see and @link tags in the description of the given descriptor to their markdown representation.
     *
     * @uses self::resolveTag()
     */
    private function resolveSeeAndLinkTags(DescriptorAbstract $descriptor): void
    {
        // store descriptor to use it in the resolveTag method
        $this->descriptor = $descriptor;

        $descriptor->setDescription(
            preg_replace_callback(
                self::REGEX_INLINE_LINK_OR_SEE_TAG,
                [$this, 'resolveTag'],
                $descriptor->getDescription()
            )
        );
    }

    /**
     * Resolves an individual tag, indicated by the results of the Regex used to extract tags.
     *
     * @param string[] $match
     * @return string|string[]
     */
    private function resolveTag(array $match)
    {
        $tagReflector = $this->createLinkOrSeeTagFromRegexMatch($match);
        if (!$tagReflector instanceof See && !$tagReflector instanceof Link) {
            return $match;
        }

        $link = $this->getLinkText($tagReflector);
        $description = (string) $tagReflector->getDescription();

        if ($this->isUrl($link)) {
            return $this->generateMarkdownLink($link, $description ?: $link);
        }

        $link = $this->resolveQsen($link);
        $element = $this->findElement($link);
        if (!$element) {
            return (string) $link;
        }

        return $this->resolveElement($element, $link, $description);
    }

    /**
     * Determines if the given link string represents a URL by checking if it is prefixed with a URI scheme.
     */
    private function isUrl(string $link): bool
    {
        return (bool) preg_match('/^[\w]+:\/\/.+$/', $link);
    }

    /**
     * Checks if the link represents a Fully Qualified Structural Element Name.
     *
     * @param Fqsen|string $link
     */
    private function isFqsen($link): bool
    {
        return $link instanceof Fqsen;
    }

    /**
     * Creates a Tag Reflector from the given array of tag line, tag name and tag content.
     *
     * @param string[] $match
     */
    private function createLinkOrSeeTagFromRegexMatch(array $match): Tag
    {
        list($completeMatch, $tagName, $tagContent) = $match;

        $fqsenResolver = new FqsenResolver();
        $tagFactory = new StandardTagFactory($fqsenResolver);
        $descriptionFactory = new DescriptionFactory($tagFactory);
        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new TypeResolver($fqsenResolver));

        switch ($tagName) {
            case 'see':
                return See::create($tagContent, $fqsenResolver, $descriptionFactory, $this->createDocBlockContext());
            case 'link':
                return Link::create($tagContent, $descriptionFactory, $this->createDocBlockContext());
        }
    }

    /**
     * Resolves a QSEN to a FQSEN.
     *
     * If a relative QSEN is provided then this method will attempt to resolve it given the current namespace and
     * namespace aliases.
     *
     * @param Fqsen|string $link
     * @return Fqsen|string
     */
    private function resolveQsen($link)
    {
        if (!$this->isFqsen($link)) {
            return $link;
        }

        return $link;
    }

    /**
     * Generates a Markdown link to the given Descriptor or returns the link text if no route to the Descriptor could
     * be matched.
     *
     * @param Fqsen|string $link
     */
    private function resolveElement(DescriptorAbstract $element, $link, ?string $description = null): string
    {
        $rule = $this->router->match($element);

        if ($rule) {
            $url = '..' . $rule->generate($element);
            $link = $this->generateMarkdownLink($url, $description ?: (string) $link);
        }

        return $link;
    }

    /**
     * Returns the link for the given reflector.
     *
     * Because the link tag and the see tag have different methods to acquire the link text we abstract that into this
     * method.
     */
    private function getLinkText(Tag $tagReflector): ?string
    {
        if ($tagReflector instanceof See) {
            return (string) $tagReflector->getReference();
        }

        if ($tagReflector instanceof Link) {
            return (string) $tagReflector->getLink();
        }

        return null;
    }

    /**
     * Tries to find an element with the given FQSEN in the elements listing for this project.
     *
     * @param Fqsen|string $fqsen
     */
    private function findElement($fqsen): ?DescriptorAbstract
    {
        return $this->elementCollection[(string) $fqsen] ?? null;
    }

    /**
     * Creates a DocBlock context containing the namespace and aliases for the current descriptor.
     */
    private function createDocBlockContext(): Context
    {
        $file = $this->descriptor->getFile();
        $namespaceAliases = $file ? $file->getNamespaceAliases()->getAll() : [];
        foreach ($namespaceAliases as $alias => $fqsen) {
            $namespaceAliases[$alias] = (string) $fqsen;
        }

        return new Context((string) $this->descriptor->getNamespace(), $namespaceAliases);
    }

    /**
     * Generates a Markdown-formatted string representing a link with a description.
     *
     * @param Fqsen|string $link
     */
    private function generateMarkdownLink($link, string $description): string
    {
        return '[' . $description . '](' . (string) $link . ')';
    }
}
