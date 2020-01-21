<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Listener;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Parser\FlySystemMirror;

final class AssetsCopyListener
{
    /** @var FilesystemInterface */
    private $targetFilesystem;

    public function __construct(FilesystemInterface $targetFilesystem)
    {
        $this->targetFilesystem = $targetFilesystem;
    }

    public function postBuildRender()
    {
        $sourceFilesystem = new Filesystem(new Local(sprintf('%s/../Templates/rtd/assets', __DIR__)));

        FlySystemMirror::mirror($sourceFilesystem, $this->targetFilesystem, '', 'assets');
    }
}
