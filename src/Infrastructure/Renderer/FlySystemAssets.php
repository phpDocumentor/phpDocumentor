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

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Asset;
use phpDocumentor\DomainModel\Renderer\AssetNotFoundException;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\DomainModel\Renderer\Template;

final class FlySystemAssets implements Assets
{
    /** @var Filesystem|MountManager */
    private $filesystem;

    /**
     * @param Filesystem|MountManager $filesystem
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

        return new Asset($this->filesystem->get((string)$location));
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
