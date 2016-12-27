<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Renderer;

use League\Flysystem\File;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Asset;
use phpDocumentor\DomainModel\Renderer\AssetNotFoundException;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\DomainModel\Renderer\Template;

final class FlySystemAssets implements Assets
{
    /** @var FilesystemInterface|MountManager */
    private $filesystem;

    /**
     * @param FilesystemInterface|MountManager $filesystem
     */
    public function __construct($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function get(Path $location)
    {
        if (! $this->has($location)) {
            throw new AssetNotFoundException(sprintf('Asset at "%s" could not found', $location));
        }

        /** @var File $flyLocation */
        $flyLocation = $this->filesystem->get((string)$location);
        if ($flyLocation->isDir()) {
            $listing = $this->filesystem->listContents((string)$location, true);
            $locations = array_map(
                function ($value) {
                    if ($value['type'] === 'dir') {
                        return null;
                    }

                    return new Path($value['path']);
                },
                $listing
            );

            return new Asset\Folder($location, array_values(array_filter($locations)));
        }

        return new Asset($flyLocation->read());
    }

    /**
     * @inheritDoc
     */
    public function has(Path $location)
    {
        return $this->filesystem->has((string)$location);
    }

    /**
     * Returns a new instance of this collection with the assets for the given template included.
     *
     * @param Template $template
     *
     * @return Assets
     */
    public function includeTemplateAssets(Template $template)
    {
        $assets = clone $this;
        if ($assets->filesystem instanceof Filesystem) {
            $assets->filesystem = new MountManager([$assets->filesystem]);
        }

        // TODO: Add template location to the mount manager
        // $assets->filesystem->mountFilesystem('', $template->)

        return $assets;
    }
}
