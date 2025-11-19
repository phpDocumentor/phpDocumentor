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
use phpDocumentor\Dsn;
use phpDocumentor\Path;

/**
 * @template-extends ArrayObject<string, mixed>&ConfigurationMap
 * @psalm-type ConfigurationMap = array{
 *     phpdocumentor: array{
 *         configVersion: string,
 *         title?: string,
 *         paths: array{output: Dsn, cache: Path},
 *         versions: array<string, VersionSpecification>,
 *         use_cache: bool,
 *         settings: array<string, mixed>,
 *         templates: non-empty-array<
 *             array{
 *                 name: string,
 *                 location: ?Path,
 *                 parameters: array<string, mixed>
 *             }
 *         >
 *     }
 * }
 */
final class Configuration extends ArrayObject
{
}
