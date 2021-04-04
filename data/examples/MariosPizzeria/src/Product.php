<?php

declare(strict_types=1);

namespace Marios;

interface Product
{
    public const PUBLIC_CONSTANT = 1;

    /**
     * @deprecated
     * @var string
     */
    protected const PROTECTED_CONSTANT = 2;

    private const PRIVATE_CONSTANT = 2;

    /**
     * Returns the name to be displayed on the product listing.
     *
     * @return string the name of this product.
     */
    public function getName(): string;
}
