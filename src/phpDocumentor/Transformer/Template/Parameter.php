<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

/**
 * Model representing a parameter in a template or transformation.
 */
class Parameter
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $value;

    /**
     * Sets an XML attribute
     *
     * @return $this for a fluent interface
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets an XML value
     *
     * @return $this for a fluent interface
     */
    public function setValue(string $value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}
