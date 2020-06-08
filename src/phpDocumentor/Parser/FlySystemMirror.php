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

namespace phpDocumentor\Parser;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

final class FlySystemMirror
{
    public static function mirror(
        FilesystemInterface $source,
        FilesystemInterface $destination,
        string $sourcePath = '',
        string $destinationPath = ''
    ) : void {
        $mountManager = new MountManager(['source' => $source, 'destination' => $destination]);

        $contents = $mountManager->listContents('source://' . $sourcePath, true);
        $mountManager->createDir('destination://' . $destinationPath);

        foreach ($contents as $fileNode) {
            if ($fileNode['type'] === 'dir') {
                $mountManager->createDir('destination://' . $destinationPath . '/' . $fileNode['path']);
                continue;
            }

            $mountManager->put(
                'destination://' . $destinationPath . '/' . $fileNode['path'],
                $mountManager->read('source://' . $sourcePath . '/' . $fileNode['path'])
            );
        }
    }
}
