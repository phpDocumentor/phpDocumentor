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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;
use phpDocumentor\Reflection\Type;

/**
 * Renders an HTML anchor pointing to the location of the provided element.
 */
class Renderer
{
    /** @var string */
    protected $destination = '';

    /** @var Queue */
    private $routers;

    /**
     * Initializes this renderer with a set of routers that are checked.
     */
    public function __construct(Queue $routers)
    {
        $this->routers = $routers;
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
     *
     * @param string $destination
     *
     * @see \phpDocumentor\Transformer\Writer\Twig for the invocation
     *     of this method.
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * Returns the target directory relative to the Project's Root.
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $value
     * @param mixed $presentation
     *
     * @return mixed
     */
    public function render($value, $presentation)
    {
        if (is_array($value) && current($value) instanceof Type) {
            return $this->renderType($value, $presentation);
        }

        if (is_array($value) || $value instanceof \Traversable || $value instanceof Collection) {
            return $this->renderASeriesOfLinks($value, $presentation);
        }

        if ($value instanceof CollectionDescriptor) {
            return $this->renderTypeCollection($value, $presentation);
        }

        return $this->renderLink($value, $presentation);
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
     * @return string|null
     */
    public function convertToRootPath($relative_path): ?string
    {
        // get the path to the root directory
        $path_parts = explode(DIRECTORY_SEPARATOR, $this->getDestination());
        $path_to_root = (count($path_parts) > 1)
            ? implode('/', array_fill(0, count($path_parts) - 1, '..')) . '/'
            : '';

        // append the relative path to the root
        if (is_string($relative_path) && ($relative_path[0] !== '@')) {
            return $path_to_root . ltrim($relative_path, '/');
        }

        $rule = $this->routers->match($relative_path);
        if (!$rule) {
            return null;
        }

        $generatedPath = $rule->generate($relative_path);

        return $generatedPath ? $path_to_root . ltrim($generatedPath, '/') : null;
    }

    /**
     * Returns a series of anchors and strings for the given collection of routable items.
     *
     * @param array|\Traversable|Collection $value
     * @param string                        $presentation
     *
     * @return string[]
     */
    protected function renderASeriesOfLinks($value, $presentation): array
    {
        if ($value instanceof Collection) {
            $value = $value->getAll();
        }

        $result = [];
        foreach ($value as $path) {
            $result[] = $this->render($path, $presentation);
        }

        return $result;
    }

    /**
     * Renders the view representation for an array or collection.
     *
     * @param CollectionDescriptor $value
     * @param string               $presentation
     *
     * @return string
     */
    protected function renderTypeCollection($value, $presentation)
    {
        $baseType = $this->render($value->getBaseType(), $presentation);
        $keyTypes = $this->render($value->getKeyTypes(), $presentation);
        $types = $this->render($value->getTypes(), $presentation);

        $arguments = [];
        if ($keyTypes) {
            $arguments[] = implode('|', $keyTypes);
        }

        $arguments[] = implode('|', $types);

        if ($value instanceof CollectionDescriptor && count($value->getKeyTypes()) === 0) {
            $typeString = (count($types) > 1) ? '(' . reset($arguments) . ')' : reset($arguments);
            $collection = $typeString . '[]';
        } else {
            $collection = ($baseType ?: $value->getName()) . '&lt;' . implode(',', $arguments) . '&gt;';
        }

        return $collection;
    }

    protected function renderLink($path, $presentation)
    {
        $url = false;
        $rule = $this->routers->match($path);
        if ($rule) {
            $generatedUrl = $rule->generate($path);
            $url = $generatedUrl ? ltrim($generatedUrl, '/') : false;
        }

        if (is_string($url)
            && $url[0] !== '/'
            && (strpos($url, 'http://') !== 0)
            && (strpos($url, 'https://') !== 0)
            && (strpos($url, 'ftp://') !== 0)
        ) {
            $url = $this->convertToRootPath($url);
        }

        switch ($presentation) {
            case 'url': // return the first url
                return $url;
            case 'class:short':
                $parts = explode('\\', (string) $path);
                $path = end($parts);
                break;
        }

        return $url ? sprintf('<a href="%s">%s</a>', $url, $path) : $path;
    }

    private function renderType($value, string $presentation): array
    {
        $result = [];
        foreach ($value as $type) {
            $result[] = (string) $type;
        }

        return $result;
    }
}
