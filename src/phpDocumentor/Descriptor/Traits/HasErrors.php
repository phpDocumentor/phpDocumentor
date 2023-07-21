<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
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
    /** @var Collection<Error> A collection of errors found during filtering. */
    protected Collection $errors;

    /**
     * Sets a list of all errors associated with this element.
     *
     * @internal should not be called by any other class than the assemblers
     *
     * @param Collection<Error> $errors
     */
    public function setErrors(Collection $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors associated with this tag.
     *
     * @return Collection<Error>
     */
    public function getErrors(): Collection
    {
        if (! isset($this->errors)) {
            $this->errors = Collection::fromClassString(Error::class);
        }

        return $this->errors;
    }
}
