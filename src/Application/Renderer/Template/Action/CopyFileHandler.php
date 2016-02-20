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

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Artefact;
use phpDocumentor\DomainModel\Renderer\Asset;
use phpDocumentor\DomainModel\Renderer\Asset\Folder;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;

class CopyFileHandler implements ActionHandler
{
    /**
     * Executes the activities that this Action represents.
     *
     * @param CopyFile|Action $action
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $this->persistAssetAtLocation(
            $action,
            $this->fetchAsset($action, $action->getSource()),
            $action->getDestination()
        );
    }

    /**
     * @param CopyFile $action
     *
     * @return Asset
     */
    private function fetchAsset(CopyFile $action, Path $source)
    {
        return $action->getRenderContext()->assets()->get($source);
    }

    /**
     * @param CopyFile $action
     * @param Asset|Folder $asset
     * @param Path $location
     *
     * @return void
     */
    private function persistAssetAtLocation(CopyFile $action, $asset, Path $location)
    {
        if ($asset instanceof Folder) {
            foreach ($asset as $path) {
                $this->persistAssetAtLocation(
                    $action,
                    $this->fetchAsset($action, $path),
                    new Path($location . substr($path, strlen((string)$asset->path())))
                );
            }
            return;
        }

        $artefact = $this->createArtefact($location, $asset);
        $action->getRenderContext()->artefacts()->persist($artefact);
    }

    /**
     * @param Path $location
     * @param Asset $asset
     *
     * @return Artefact
     */
    private function createArtefact(Path $location, Asset $asset)
    {
        return new Artefact($location, $asset->content());
    }
}
