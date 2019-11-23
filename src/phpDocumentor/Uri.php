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

namespace phpDocumentor;

use InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function __construct(string $uri)
    {
        $uri = $this->addFileSchemeWhenSchemeIsAbsent($uri);

        $this->validateUri($uri);

        $this->uri = $uri;
    }

    /**
     * Returns a string representation of the uri.
     */
    public function __toString(): string
    {
        return $this->uri;
    }

    /**
     * Checks if the provided uri is equal to the current uri.
     */
    public function equals(self $other): bool
    {
        return $other->uri === $this->uri;
    }

    /**
     * Checks if $uri is valid.
     *
     * @throws InvalidArgumentException if $uri is not a valid uri.
     */
    private function validateUri(string $uri): void
    {
        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException(sprintf('%s is not a valid uri', $uri));
        }
    }

    /**
     * Checks if a scheme is present.
     * If no scheme is found, it is assumed that a local path is used, and file:// is prepended.
     */
    private function addFileSchemeWhenSchemeIsAbsent(string $uri): string
    {
        $scheme = parse_url($uri, PHP_URL_SCHEME);

        if (empty($scheme)) {
            $uri = 'file://' . $uri;
        } elseif (preg_match('/^[a-z]$/i', (string) $scheme)) { // windows driver letter
            $uri = 'file:///' . $uri;
        }

        return $uri;
    }
}
