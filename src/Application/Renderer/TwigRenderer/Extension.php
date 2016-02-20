<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\TwigRenderer;

use phpDocumentor\DomainModel\Renderer\Router\Queue;
use phpDocumentor\DomainModel\Renderer\Router\Renderer;
use phpDocumentor\DomainModel\ReadModel\ReadModels;

class Extension extends \Twig_Extension
{
    /** @var ReadModels */
    protected $views = null;

    /** @var Renderer */
    protected $routeRenderer;

    /**
     * Registers the structure.
     */
    public function __construct(ReadModels $views)
    {
        $this->views          = $views;
        $this->routeRenderer = new Renderer(new Queue());
    }

    /**
     * Returns the name of this extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'phpdocumentor';
    }

    /**
     * Sets the router used to render the URL where a Descriptor can be found.
     *
     * @param Queue $routers
     *
     * @return void
     */
    public function setRouters($routers)
    {
        $this->routeRenderer->setRouters($routers);
    }

    /**
     * Sets the destination directory relative to the Project's Root.
     *
     * The destination is the target directory containing the resulting
     * file. This destination is relative to the Project's root and can
     * be used for the calculation of nesting depths, etc.
     *
     * @param string $destination
     *
     * @see phpDocumentor\Application\Renderer\Action\TwigHandler for the invocation of this method.
     *
     * @return void
     */
    public function setDestination($destination)
    {
        $this->routeRenderer->setDestination($destination);
    }

    /**
     * Returns the target directory relative to the Project's Root.
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->routeRenderer->getDestination();
    }

    /**
     * Returns an array of global variables to inject into a Twig template.
     *
     * @return mixed[]
     */
    public function getGlobals()
    {
        return $this->views->getArrayCopy();
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
     * @return \Twig_FunctionInterface[]
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('path', array($this->routeRenderer, 'convertToRootPath'))
        );
    }

    /**
     * Returns a list of all filters that are exposed by this extension.
     *
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        $parser = \Parsedown::instance();
        $routeRenderer = $this->routeRenderer;

        return array(
            'markdown' => new \Twig_SimpleFilter(
                'markdown',
                function ($value) use ($parser) {
                    return $parser->text($value);
                }
            ),
            'route' => new \Twig_SimpleFilter(
                'route',
                function ($value, $presentation = 'normal') use ($routeRenderer) {
                    return $routeRenderer->render($value, $presentation);
                }
            ),
            'sort' => new \Twig_SimpleFilter(
                'sort_*',
                function ($direction, $collection) {
                    if (!$collection instanceof ReadModels) {
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
                            if ($direction === 'asc' && $aElem > $bElem || $direction === 'desc' && $aElem < $bElem) {
                                return 1;
                            }

                            return -1;
                        }
                    );

                    return $iterator;
                }
            ),
        );
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
     *
     * @param string $relative_path
     *
     * @return string
     */
    public function convertToRootPath($relative_path)
    {
        return $this->routeRenderer->convertToRootPath($relative_path);
    }
}
