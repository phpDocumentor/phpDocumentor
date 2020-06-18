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

namespace phpDocumentor\Compiler\Pass;

use InvalidArgumentException;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Compiler\Linker\DescriptorRepository;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Transformer\Router\Router;
use Webmozart\Assert\Assert;
use function preg_match;
use function preg_replace_callback;
use function sprintf;

/**
 * This step in the compilation process iterates through all elements and scans their descriptions for an inline `@see`
 * or `@link` tag and resolves them to a markdown link.
 */
final class ResolveInlineLinkAndSeeTags implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 9002;

    public const REGEX_INLINE_LINK_OR_SEE_TAG = '/\{\@(see|link)[\ ]+([^\}]+)\}/';

    /** @var Router */
    private $router;

    /** @var DescriptorAbstract */
    private $descriptor;

    /** @var Collection<DescriptorAbstract> */
    private $elementCollection;

    /** @var DescriptorRepository */
    private $descriptorRepository;

    /** @var TagFactory */
    private $tagFactory;

    /**
     * Registers the router with this pass.
     */
    public function __construct(
        Router $router,
        DescriptorRepository $descriptorRepository,
        TagFactory $tagFactory
    ) {
        $this->router = $router;
        $this->descriptorRepository = $descriptorRepository;
        $this->tagFactory = $tagFactory;
    }

    public function getDescription() : string
    {
        return 'Resolve @link and @see tags in descriptions';
    }

    /**
     * Iterates through each element in the project and replaces its inline @see and @link tag with a markdown
     * representation.
     */
    public function execute(ProjectDescriptor $project) : void
    {
//        $this->elementCollection = $project->getIndexes()->get('elements');
//
//        foreach ($this->elementCollection as $descriptor) {
//            $this->resolveSeeAndLinkTags($descriptor);
//        }
    }

    /**
     * Resolves all @see and @link tags in the description of the given descriptor to their markdown representation.
     *
     * @uses self::resolveTag()
     */
    private function resolveSeeAndLinkTags(DescriptorAbstract $descriptor) : void
    {
        // store descriptor to use it in the resolveTag method
        $this->descriptor = $descriptor;

        $descriptor->setDescription(
            preg_replace_callback(
                self::REGEX_INLINE_LINK_OR_SEE_TAG,
                /** @param list<string> $match */
                function (array $match) {
                    return $this->resolveTag($match);
                },
                $descriptor->getDescription()
            )
        );
    }

    /**
     * Resolves an individual tag, indicated by the results of the Regex used to extract tags.
     *
     * @param list<string> $match
     *
     * @return string|list<string>
     */
    private function resolveTag(array $match)
    {
        // The checks below are impossible to reproduce in a test because of the regular expression in
        // the resolveSeeAndLinkTags method
        // @codeCoverageIgnoreStart
        try {
            $tagReflector = $this->createLinkOrSeeTagFromRegexMatch($match);
        } catch (InvalidArgumentException $e) {
            return $match;
        }

        if (!$tagReflector instanceof BaseTag) {
            return $match;
        }

        // @codeCoverageIgnoreEnd

        $link = $this->getLinkText($tagReflector);
        $description = (string) $tagReflector->getDescription();

        if ($this->isUrl($link)) {
            return $this->generateMarkdownLink($link, $description ?: $link);
        }

        $fqsen = (string) $link;
        $element = $this->descriptorRepository->findAlias($fqsen, $this->descriptor);
        if (!$element instanceof DescriptorAbstract) {
            return $fqsen;
        }

        return $this->resolveElement($element, $fqsen, $description);
    }

    /**
     * Determines if the given link string represents a URL by checking if it is prefixed with a URI scheme.
     */
    private function isUrl(string $link) : bool
    {
        return (bool) preg_match('/^[\w]+:\/\/.+$/', $link);
    }

    /**
     * Creates a Tag Reflector from the given array of tag line, tag name and tag content.
     *
     * @param list<string> $match
     */
    private function createLinkOrSeeTagFromRegexMatch(array $match) : Tag
    {
        [, $tagName, $tagContent] = $match;

        Assert::oneOf(
            $tagName,
            ['see', 'link'],
            sprintf('Tag with name: "%s" cannot be used to create a link', $tagName)
        );

        return $this->tagFactory->create('@' . $tagName . ' ' . $tagContent, $this->createDocBlockContext());
    }

    /**
     * Generates a Markdown link to the given Descriptor or returns the link text if no route to the Descriptor could
     * be matched.
     */
    private function resolveElement(DescriptorAbstract $element, string $link, ?string $description = null) : string
    {
        $url = $this->router->generate($element);
        if ($url) {
            $url = '..' . $url;
            $link = $this->generateMarkdownLink($url, $description ?: $link);
        }

        return $link;
    }

    /**
     * Returns the link for the given reflector.
     *
     * Because the link tag and the see tag have different methods to acquire the link text we abstract that into this
     * method.
     */
    private function getLinkText(Tag $tagReflector) : ?string
    {
        if ($tagReflector instanceof See) {
            return (string) $tagReflector->getReference();
        }

        if ($tagReflector instanceof Link) {
            return $tagReflector->getLink();
        }

        return null;
    }

    /**
     * Creates a DocBlock context containing the namespace and aliases for the current descriptor.
     */
    private function createDocBlockContext() : Context
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
    private function generateMarkdownLink($link, string $description) : string
    {
        return '[' . $description . '](' . (string) $link . ')';
    }
}
