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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Validation\Error;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

trait HasErrors
{
    /** @var CollectionInterface<Error> A collection of errors found during filtering. */
    protected CollectionInterface $errors;

    /**
     * Sets a list of all errors associated with this element.
     *
     * @internal should not be called by any other class than the assemblers
     *
     * @param CollectionInterface<Error> $errors
     */
    public function setErrors(CollectionInterface $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors associated with this tag.
     *
     * @return CollectionInterface<Error>
     */
    public function getErrors(): CollectionInterface
    {
        if (! isset($this->errors)) {
            $this->errors = Collection::fromClassString(Error::class);
        }

        return $this->errors;
    }
}
