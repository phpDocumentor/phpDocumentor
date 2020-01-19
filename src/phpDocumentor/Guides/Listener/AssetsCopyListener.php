<?php

declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Listener;

use Symfony\Component\Filesystem\Filesystem;

final class AssetsCopyListener
{
    /** @var string */
    private $targetDir;

    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function postBuildRender()
    {
        $fs = new Filesystem();
        $fs->mirror(
            sprintf('%s/../Templates/rtd/assets', __DIR__),
            sprintf('%s/assets', $this->targetDir)
        );
    }
}
