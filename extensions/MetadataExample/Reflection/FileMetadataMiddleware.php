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

namespace phpDocumentor\MetadataExample\Reflection;

use Override;
use phpDocumentor\Reflection\Metadata\MetaDataContainer;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;

use function method_exists;

/** The reducers of the reflection package are not applied to files; middleware is used for those instead. */
final class FileMetadataMiddleware implements Middleware
{
    #[Override]
    public function execute(Command $command, callable $next): object
    {
        $file = $next($command);

        if ($file instanceof MetaDataContainer && method_exists($file, 'getName')) {
            $file->addMetadata(new ExampleMetadata('metadata-for-' . $file->getName()));
            $file->addMetadata(new SecondMetadata('second-for-' . $file->getName()));
        }

        return $file;
    }
}
