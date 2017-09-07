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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
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
use phpDocumentor\Transformer\Router\RouterAbstract;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * This step in the compilation process iterates through all elements and scans their descriptions for an inline `@see`
 * or `@link` tag and resolves them to a markdown link.
 *
 */
class ResolveInlineLinkAndSeeTags implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9002;
    const REGEX_INLINE_LINK_OR_SEE_TAG = '/\{\@(see|link)[\ ]+([^\}]+)\}/';

    /** @var RouterAbstract */
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

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Resolve @link and @see tags in descriptions';
    }

    /**
     * Iterates through each element in the project and replaces its inline @see and @link tag with a markdown
     * representation.
     *
     * @param ProjectDescriptor $project
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
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
     * @param DescriptorAbstract $descriptor
     *
     * @uses self::resolveTag()
     *
     * @return void
     */
    private function resolveSeeAndLinkTags(DescriptorAbstract $descriptor)
    {
        // store descriptor to use it in the resolveTag method
        $this->descriptor = $descriptor;

        $descriptor->setDescription(
            preg_replace_callback(
                self::REGEX_INLINE_LINK_OR_SEE_TAG,
                array($this, 'resolveTag'),
                $descriptor->getDescription()
            )
        );
    }

    /**
     * Resolves an individual tag, indicated by the results of the Regex used to extract tags.
     *
     * @param string[] $match
     *
     * @return string
     */
    private function resolveTag($match)
    {
        $tagReflector = $this->createLinkOrSeeTagFromRegexMatch($match);
        if (!$tagReflector instanceof See && !$tagReflector instanceof Link) {
            return $match;
        }

        $link        = $this->getLinkText($tagReflector);
        $description = $tagReflector->getDescription();

        if ($this->isUrl($link)) {
            return $this->generateMarkdownLink($link, $description ?: $link);
        }

        $link    = $this->resolveQsen($link);
        $element = $this->findElement($link);
        if (!$element) {
            return $link;
        }

        return $this->resolveElement($element, $link, $description);
    }

    /**
     * Determines if the given link string represents a URL by checking if it is prefixed with a URI scheme.
     *
     * @param string $link
     *
     * @return boolean
     */
    private function isUrl($link)
    {
        return (bool) preg_match('/^[\w]+:\/\/.+$/', $link);
    }

    /**
     * Checks if the link represents a Fully Qualified Structural Element Name.
     *
     * @param string $link
     *
     * @return bool
     */
    private function isFqsen($link)
    {
        return $link instanceof Fqsen;
    }

    /**
     * Creates a Tag Reflector from the given array of tag line, tag name and tag content.
     *
     * @param string[] $match
     *
     * @return Tag
     */
    private function createLinkOrSeeTagFromRegexMatch(array $match)
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
     * @param string $link
     *
     * @return string
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
     * @param DescriptorAbstract $element
     * @param string             $link
     * @param string             $description
     *
     * @return string
     */
    private function resolveElement(DescriptorAbstract $element, $link, $description)
    {
        $rule = $this->router->match($element);

        if ($rule) {
            $url = '..' . $rule->generate($element);
            $link = $this->generateMarkdownLink($url, $description ? : $link);
        }

        return $link;
    }

    /**
     * Returns the link for the given reflector.
     *
     * Because the link tag and the see tag have different methods to acquire the link text we abstract that into this
     * method.
     *
     * @param See|Link $tagReflector
     *
     * @return string
     */
    private function getLinkText($tagReflector)
    {
        if ($tagReflector instanceof See) {
            return $tagReflector->getReference();
        }

        if ($tagReflector instanceof Link) {
            return $tagReflector->getLink();
        }

        return null;
    }

    /**
     * Tries to find an element with the given FQSEN in the elements listing for this project.
     *
     * @param string $fqsen
     *
     * @return DescriptorAbstract|null
     */
    private function findElement($fqsen)
    {
        return isset($this->elementCollection[(string)$fqsen]) ? $this->elementCollection[(string)$fqsen] : null;
    }

    /**
     * Creates a DocBlock context containing the namespace and aliases for the current descriptor.
     *
     * @return Context
     */
    private function createDocBlockContext()
    {
        $file = $this->descriptor->getFile();
        $namespaceAliases = $file ? $file->getNamespaceAliases()->getAll() : array();
        foreach ($namespaceAliases as $alias => $fqsen) {
            $namespaceAliases[$alias] = (string)$fqsen;
        }

        return new Context((string)$this->descriptor->getNamespace(), $namespaceAliases);
    }

    /**
     * Generates a Markdown-formatted string representing a link with a description.
     *
     * @param string $link
     * @param string $description
     *
     * @return string
     */
    private function generateMarkdownLink($link, $description)
    {
        return '[' . $description . '](' . $link . ')';
    }
}
