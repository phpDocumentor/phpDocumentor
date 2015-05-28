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

namespace phpDocumentor\Renderer\Template;

/**
 * Value object representing a parameter provided with a Template or Action.
 */
final class Parameter
{
    /** @var string The name, or key, for this parameter. */
    private $key = '';

    /** @var string The value provided with this parameter. */
    private $value = '';

    /**
     * Initializes this parameter with the given key and value, and asserts that they are strings.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(
                'The key for a parameter is supposed to be a string, received ' . var_export($key, true)
            );
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException(
                'The value for a parameter is supposed to be a string, received ' . var_export($value, true)
            );
        }

        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * Returns the name, or key, for this parameter.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the value for this parameter.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
