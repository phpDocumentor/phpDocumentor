<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel;

/**
 * Value object for uri.
 * Uri can be either local or remote.
 */
final class Uri
{
    /**
     * Uri path.
     *
     * @var string
     */
    private $uri;

    /**
     * Initializes the Uri.
     *
     * @param string $uri
     * @throws \InvalidArgumentException
     */
    public function __construct($uri)
    {
        $this->validateString($uri);

        $uri = $this->addFileSchemeWhenSchemeIsAbsent($uri);

        $this->validateUri($uri);

        $this->uri = $uri;
    }

    /**
     * Returns a string representation of the uri.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->uri;
    }

    /**
     * Checks if the provided uri is equal to the current uri.
     *
     * @param Uri $other
     */
    public function equals($other): bool
    {
        return $other == $this;
    }

    /**
     * Checks if $uri is of type string.
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException if $uri is not a string.
     */
    private function validateString($uri)
    {
        if (!\is_string($uri)) {
            throw new \InvalidArgumentException(sprintf('String required, %s given', \gettype($uri)));
        }
    }

    /**
     * Checks if $uri is valid.
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException if $uri is not a valid uri.
     */
    private function validateUri($uri)
    {
        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid uri', $uri));
        }
    }

    /**
     * Checks if a scheme is present.
     * If no scheme is found, it is assumed that a local path is used, and file:// is prepended.
     *
     * @param string $uri
     */
    private function addFileSchemeWhenSchemeIsAbsent($uri): string
    {
        $scheme = parse_url($uri, PHP_URL_SCHEME);

        if (preg_match('/^[a-z]$/i', $scheme)) { // windows driver letter
            $uri = 'file:///' . $uri;
        } elseif (empty($scheme)) {
            $uri = 'file://' . $uri;
        }

        return $uri;
    }
}
