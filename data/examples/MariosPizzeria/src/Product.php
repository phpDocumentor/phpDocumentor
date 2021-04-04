<?php

declare(strict_types=1);

namespace Marios;

interface Product
{
    /**
     * Returns the name to be displayed on the product listing.
     *
     * @return string the name of this product.
     */
    public function getName(): string;
}
