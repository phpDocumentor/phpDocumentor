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

use ArrayIterator;
use League\CommonMark\MarkdownConverterInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Descriptor\Tag\LinkDescriptor;
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function array_unshift;
use function count;
use function method_exists;
use function sprintf;
use function str_replace;
use function strtolower;
use function var_export;
use function vsprintf;

/**
 * Basic extension adding phpDocumentor specific functionality for Twig
 * templates.
 *
 * Global variables:
 *
 * - *ast_node*, the current $data element
 *
 * Functions:
 *
 * - *path(string) *, converts the given relative path to be based of the projects
 *   root instead of the current directory
 *
 * Filters:
 *
 * - *markdown*, converts the associated text from Markdown formatting to HTML.
 * - *trans*, translates the given string
 * - *route*, attempts to generate a URL for a given Descriptor
 * - *sort_desc*, sorts the given objects by their Name property/getter in a descending fashion
 * - *sort_asc*, sorts the given objects by their Name property/getter in a ascending fashion
 */
final class Extension extends AbstractExtension implements ExtensionInterface, GlobalsInterface
{
    /** @var ProjectDescriptor */
    private $data;

    /** @var LinkRenderer */
    private $routeRenderer;

    /** @var MarkdownConverterInterface */
    private $markdownConverter;

    /**
     * Registers the structure and transformation with this extension.
     *
     * @param ProjectDescriptor $project Represents the complete Abstract Syntax Tree.
     */
    public function __construct(
        ProjectDescriptor $project,
        MarkdownConverterInterface $markdownConverter,
        LinkRenderer $routeRenderer
    ) {
        $this->data = $project;
        $this->routeRenderer = $routeRenderer->withProject($project);
        $this->markdownConverter = $markdownConverter;
    }

    /**
     * Sets the destination directory relative to the Project's Root.
     *
     * The destination is the target directory containing the resulting
     * file. This destination is relative to the Project's root and can
     * be used for the calculation of nesting depths, etc.
     *
     * @see EnvironmentFactory for the invocation of this method.
     */
    public function setDestination(string $destination) : void
    {
        $this->routeRenderer = $this->routeRenderer->withDestination($destination);
    }

    /**
     * Returns an array of global variables to inject into a Twig template.
     *
     * @return array<string, ProjectDescriptor|bool>
     */
    public function getGlobals() : array
    {
        return [
            'project' => $this->data,
            'usesNamespaces' => count($this->data->getNamespace()->getChildren()) > 0,
            'usesPackages' => count($this->data->getPackage()->getChildren()) > 1,
        ];
    }

    /**
     * Returns a listing of all functions that this extension adds.
     *
     * This method is automatically used by Twig upon registering this
     * extension (which is done automatically by phpDocumentor) to determine
     * an additional list of functions.
     *
     * See the Class' DocBlock for a listing of functionality added by this
     * Extension.
     *
     * @return TwigFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction(
                'renderBaseUrlHeader',
                function () : string {
                    $this->routeRenderer = $this->routeRenderer->doNotConvertUrlsToRootPath();

                    $absolutePath = $this->routeRenderer->convertToRootPath('/', true);
                    if (!$absolutePath) {
                        return '';
                    }

                    return '<base href="' . $absolutePath . '">';
                },
                ['is_safe' => ['all']]
            ),
            new TwigFunction(
                'path',
                function (string $url) : string {
                    return $this->routeRenderer->convertToRootPath($url);
                }
            ),
            new TwigFunction(
                'link',
                function (object $element) : string {
                    return $this->routeRenderer->link($element);
                }
            ),
            new TwigFunction(
                'breadcrumbs',
                static function (DescriptorAbstract $baseNode) : array {
                    $results = [];
                    $namespace = $baseNode instanceof NamespaceDescriptor
                        ? $baseNode->getParent()
                        : $baseNode->getNamespace();
                    while ($namespace instanceof NamespaceDescriptor && $namespace->getName() !== '\\') {
                        array_unshift($results, $namespace);
                        $namespace = $namespace->getParent();
                    }

                    return $results;
                }
            ),
            new TwigFunction(
                'packages',
                static function (DescriptorAbstract $baseNode) : array {
                    $results = [];
                    $package = $baseNode instanceof PackageDescriptor
                        ? $baseNode->getParent()
                        : $baseNode->getPackage();
                    while ($package instanceof PackageDescriptor && $package->getName() !== '\\') {
                        array_unshift($results, $package);
                        $package = $package->getParent();
                    }

                    return $results;
                }
            ),
            new TwigFunction(
                'methods',
                static function (DescriptorAbstract $descriptor) : Collection {
                    $methods = new Collection();
                    if (method_exists($descriptor, 'getInheritedMethods')) {
                        $methods = $methods->merge($descriptor->getInheritedMethods());
                    }

                    if (method_exists($descriptor, 'getMagicMethods')) {
                        $methods = $methods->merge($descriptor->getMagicMethods());
                    }

                    if (method_exists($descriptor, 'getMethods')) {
                        $methods = $methods->merge($descriptor->getMethods());
                    }

                    return $methods;
                }
            ),
            new TwigFunction(
                'properties',
                static function (DescriptorAbstract $descriptor) : Collection {
                    $properties = new Collection();
                    if (method_exists($descriptor, 'getInheritedProperties')) {
                        $properties = $properties->merge($descriptor->getInheritedProperties());
                    }

                    if (method_exists($descriptor, 'getMagicProperties')) {
                        $properties = $properties->merge($descriptor->getMagicProperties());
                    }

                    if (method_exists($descriptor, 'getProperties')) {
                        $properties = $properties->merge($descriptor->getProperties());
                    }

                    return $properties;
                }
            ),
            new TwigFunction(
                'constants',
                static function (DescriptorAbstract $descriptor) : Collection {
                    $constants = new Collection();
                    if (method_exists($descriptor, 'getInheritedConstants')) {
                        $constants = $constants->merge($descriptor->getInheritedConstants());
                    }

                    if (method_exists($descriptor, 'getMagicConstants')) {
                        $constants = $constants->merge($descriptor->getMagicConstants());
                    }

                    if (method_exists($descriptor, 'getConstants')) {
                        $constants = $constants->merge($descriptor->getConstants());
                    }

                    return $constants;
                }
            ),
        ];
    }

    /**
     * Returns a list of all filters that are exposed by this extension.
     *
     * @return TwigFilter[]
     */
    public function getFilters() : array
    {
        $routeRenderer = $this->routeRenderer;

        return [
            'markdown' => new TwigFilter(
                'markdown',
                function (?string $value) : string {
                    return str_replace(
                        ['<pre>', '<code>'],
                        ['<pre class="prettyprint">', '<code class="prettyprint">'],
                        $this->markdownConverter->convertToHtml($value ?? '')
                    );
                },
                ['is_safe' => ['all']]
            ),
            'trans' => new TwigFilter(
                'trans',
                static function ($value) {
                    return $value;
                }
            ),
            'route' => new TwigFilter(
                'route',
                function ($value, string $presentation = LinkRenderer::PRESENTATION_NORMAL) {
                    return $this->routeRenderer->render($value, $presentation);
                },
                ['is_safe' => ['all']]
            ),
            'sort' => new TwigFilter(
                'sort_*',
                /** @var Collection<Descriptor> $collection */
                static function (string $direction, Collection $collection) : ArrayIterator {
                    $iterator = $collection->getIterator();
                    $iterator->uasort(
                        static function (Descriptor $a, Descriptor $b) use ($direction) {
                            $aElem = strtolower($a->getName());
                            $bElem = strtolower($b->getName());
                            if ($aElem === $bElem) {
                                return 0;
                            }

                            if (($direction === 'asc' && $aElem > $bElem) ||
                                ($direction === 'desc' && $aElem < $bElem)
                            ) {
                                return 1;
                            }

                            return -1;
                        }
                    );

                    return $iterator;
                }
            ),
            'sortByVisibility' => new TwigFilter(
                'sortByVisibility',
                /** @var Collection<Descriptor> $collection */
                static function (Collection $collection) : ArrayIterator {
                    $visibilityOrder = [
                        'public' => 0,
                        'protected' => 1,
                        'private' => 2,
                    ];
                    $iterator = $collection->getIterator();
                    $iterator->uasort(
                        static function (Descriptor $a, Descriptor $b) use ($visibilityOrder) {
                            $prio = 0;
                            if ($a instanceof VisibilityInterface && $b instanceof VisibilityInterface) {
                                $visibilityPriorityA = $visibilityOrder[$a->getVisibility()] ?? 0;
                                $visibilityPriorityB = $visibilityOrder[$b->getVisibility()] ?? 0;
                                $prio = $visibilityPriorityA <=> $visibilityPriorityB;
                            }

                            if ($prio !== 0) {
                                return $prio;
                            }

                            $aElem = strtolower($a->getName());
                            $bElem = strtolower($b->getName());

                            return $aElem <=> $bElem;
                        }
                    );

                    return $iterator;
                }
            ),
            'export' => new TwigFilter(
                'export',
                static function ($var) {
                    return var_export($var, true);
                }
            ),
            'description' => new TwigFilter(
                'description',
                function (?DescriptionDescriptor $description) {
                    if ($description === null || $description->getBodyTemplate() === '') {
                        return '';
                    }

                    $tagStrings = [];
                    foreach ($description->getTags() as $tag) {
                        if ($tag instanceof SeeDescriptor) {
                            $tagStrings[] = $this->routeRenderer->render(
                                $tag->getReference(),
                                LinkRenderer::PRESENTATION_CLASS_SHORT
                            );
                        } elseif ($tag instanceof LinkDescriptor) {
                            $tagStrings[] = sprintf('[%s](%s)', $tag->getDescription(), $tag->getLink());
                        } elseif ($tag instanceof ExampleDescriptor) {
                            $tagStrings[] = $tag->getDescription() . "\n"
                                . '```php' . "\n" . $tag->getExample() . "\n" . '```';
                        } else {
                            $tagStrings[] = (string) $tag;
                        }
                    }

                    return vsprintf($description->getBodyTemplate(), $tagStrings);
                }
            ),
        ];
    }
}
