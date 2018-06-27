<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Parser;

use phpDocumentor\Parser\Middleware\CacheMiddleware;
use phpDocumentor\Parser\Parser;
use Stash\Driver\FileSystem;
use Stash\Pool;

final class CacheFactory
{
    public static function create(Parser $parser): CacheMiddleware
    {
        return new CacheMiddleware(
            new Pool(new FileSystem(['path' => 'build/api-cache'])),
            $parser
        );
    }
}
