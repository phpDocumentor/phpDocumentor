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

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

/**
 * Abstract class for Descriptors with a type and variable name.
 */
abstract class TypedVariableAbstract extends TypedAbstract
{
    /** @var string variableName */
    protected $variableName = '';

    /**
     * Retrieves the variable name stored on this descriptor.
     */
    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * Sets the variable name on this descriptor.
     */
    public function setVariableName(string $variableName): void
    {
        $this->variableName = $variableName;
    }
}
