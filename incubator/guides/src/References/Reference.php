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

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\ReferenceBuilder;

/**
 * A reference is something that can be resolved in the document, for instance:
 *
 * :method:`helloWorld()`
 *
 * Will be resolved as a reference of type method and the given reference will
 * be called to resolve it
 *
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 */
abstract class Reference
{
    /**
     * Returns the domain for the role of this reference, or empty in case no domain was detected.
     *
     * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/domains.html
     */
    public function getDomain(): string
    {
        return '';
    }

    /**
     * The name of the reference, i.e the :something:
     */
    abstract public function getName(): string;

    public function getRole(): string
    {
        $role = $this->getName();
        if ($this->getDomain()) {
            $role = $this->getDomain() . ':' . $role;
        }

        return $role;
    }

    /**
     * Resolve the reference and returns an array
     *
     * @param Environment $environment the Environment in use
     * @param string $data the data of the reference
     */
    abstract public function resolve(Environment $environment, string $data): ?ResolvedReference;

    /**
     * Called when a reference is just found
     */
    public function found(ReferenceBuilder $referenceRegistry, string $data): void
    {
    }
}
