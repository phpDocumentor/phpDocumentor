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

namespace phpDocumentor\Configuration;

use ArrayObject;

/**
 * @template-extends ArrayObject<string, mixed>
 */
final class Configuration extends ArrayObject
{
    /** @return VersionSpecification[] */
    public function getVersions(): array
    {
        return $this['phpdocumentor']['versions'];
    }
}
