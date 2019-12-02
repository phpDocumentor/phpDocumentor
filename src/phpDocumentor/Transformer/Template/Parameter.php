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

namespace phpDocumentor\Transformer\Template;

/**
 * Model representing a parameter in a template or transformation.
 */
final class Parameter
{
    /** @var string */
    private $key = '';

    /** @var string */
    private $value = '';

    /**
     * Sets an XML attribute
     *
     * @return $this for a fluent interface
     */
    public function setKey(string $key) : self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * Sets an XML value
     *
     * @return $this for a fluent interface
     */
    public function setValue(string $value) : self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
