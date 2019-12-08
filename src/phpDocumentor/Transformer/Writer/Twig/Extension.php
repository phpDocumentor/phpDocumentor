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

namespace phpDocumentor\Transformer\Writer\Twig;

use Parsedown;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use function strtolower;

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
    protected $data;

    /** @var Renderer */
    protected $routeRenderer;

    /**
     * Registers the structure and transformation with this extension.
     *
     * @param ProjectDescriptor $project        Represents the complete Abstract Syntax Tree.
     * @param Transformation    $transformation Represents the transformation meta data used in the current generation
     *        cycle.
     */
    public function __construct(
        ProjectDescriptor $project,
        Transformation $transformation,
        ?Renderer $routeRenderer = null
    ) {
        $this->data          = $project;
        $this->routeRenderer = $routeRenderer;
    }

    /**
     * Sets the destination directory relative to the Project's Root.
     *
     * The destination is the target directory containing the resulting
     * file. This destination is relative to the Project's root and can
     * be used for the calculation of nesting depths, etc.
     *
     * @see Writer\Twig for the invocation of this method.
     */
    public function setDestination(string $destination) : void
    {
        $this->routeRenderer->setDestination($destination);
    }

    /**
     * Returns an array of global variables to inject into a Twig template.
     *
     * @return mixed[]
     */
    public function getGlobals() : array
    {
        return [
            'project' => $this->data,
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
            new TwigFunction('path', [$this->routeRenderer, 'convertToRootPath']),
        ];
    }

    /**
     * Returns a list of all filters that are exposed by this extension.
     *
     * @return TwigFilter[]
     */
    public function getFilters() : array
    {
        $parser        = Parsedown::instance();
        $routeRenderer = $this->routeRenderer;

        return [
            'markdown' => new TwigFilter(
                'markdown',
                static function ($value) use ($parser) {
                    return $parser->text($value);
                }
            ),
            'trans' => new TwigFilter(
                'trans',
                static function ($value) {
                    return $value;
                }
            ),
            'route' => new TwigFilter(
                'route',
                static function ($value, $presentation = 'normal') use ($routeRenderer) {
                    return $routeRenderer->render($value, $presentation);
                }
            ),
            'sort' => new TwigFilter(
                'sort_*',
                static function ($direction, $collection) {
                    if (!$collection instanceof Collection) {
                        return $collection;
                    }

                    $iterator = $collection->getIterator();
                    $iterator->uasort(
                        static function ($a, $b) use ($direction) {
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
        ];
    }
}
