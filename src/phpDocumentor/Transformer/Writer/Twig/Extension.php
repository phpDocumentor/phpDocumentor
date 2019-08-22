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

namespace phpDocumentor\Transformer\Writer\Twig;

use Parsedown;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Router\Renderer;
use phpDocumentor\Transformer\Transformation;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

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
class Extension extends Twig_Extension implements ExtensionInterface, Twig_Extension_GlobalsInterface
{
    /**
     * @var ProjectDescriptor
     */
    protected $data;

    /** @var Renderer */
    protected $routeRenderer;

    /**
     * Registers the structure and transformation with this extension.
     *
     * @param ProjectDescriptor $project        Represents the complete Abstract Syntax Tree.
     * @param Transformation    $transformation Represents the transformation meta data used in the current generation
     *     cycle.
     */
    public function __construct(ProjectDescriptor $project, Transformation $transformation, Renderer $routeRenderer = null)
    {
        $this->data = $project;
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
    public function setDestination(string $destination): void
    {
        $this->routeRenderer->setDestination($destination);
    }

    /**
     * Returns the target directory relative to the Project's Root.
     */
    public function getDestination(): string
    {
        return $this->routeRenderer->getDestination();
    }

    /**
     * Returns an array of global variables to inject into a Twig template.
     *
     * @return mixed[]
     */
    public function getGlobals(): array
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
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('path', [$this->routeRenderer, 'convertToRootPath']),
        ];
    }

    /**
     * Returns a list of all filters that are exposed by this extension.
     *
     * @return Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        $parser = Parsedown::instance();
        $routeRenderer = $this->routeRenderer;

        return [
            'markdown' => new Twig_SimpleFilter(
                'markdown',
                function ($value) use ($parser) {
                    return $parser->text($value);
                }
            ),
            'trans' => new Twig_SimpleFilter(
                'trans',
                function ($value) {
                    return $value;
                }
            ),
            'route' => new Twig_SimpleFilter(
                'route',
                function ($value, $presentation = 'normal') use ($routeRenderer) {
                    return $routeRenderer->render($value, $presentation);
                }
            ),
            'sort' => new Twig_SimpleFilter(
                'sort_*',
                function ($direction, $collection) {
                    if (!$collection instanceof Collection) {
                        return $collection;
                    }

                    $iterator = $collection->getIterator();
                    $iterator->uasort(
                        function ($a, $b) use ($direction) {
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

    /**
     * Converts the given path to be relative to the root of the documentation
     * target directory.
     *
     * It is not possible to use absolute paths in documentation templates since
     * they may be used locally, or in a subfolder. As such we need to calculate
     * the number of levels to go up from the current document's directory and
     * then append the given path.
     *
     * For example:
     *
     *     Suppose you are in <root>/classes/my/class.html and you want open
     *     <root>/my/index.html then you provide 'my/index.html' to this method
     *     and it will convert it into ../../my/index.html (<root>/classes/my is
     *     two nesting levels until the root).
     *
     * This method does not try to normalize or optimize the paths in order to
     * save on development time and performance, and because it adds no real
     * value.
     */
    public function convertToRootPath(string $relative_path): string
    {
        return $this->routeRenderer->convertToRootPath($relative_path);
    }
}
