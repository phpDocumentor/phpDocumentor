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

namespace phpDocumentor\JsonPath\Fixtures;

/**
 * A different kind of store, used to verify that `type(@) == '...'` filters correctly narrow
 * down a collection to only the elements of the expected class.
 */
class PopupStore extends Store
{
}
