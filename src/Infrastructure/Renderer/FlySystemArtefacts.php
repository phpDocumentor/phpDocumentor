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
use phpDocumentor\DomainModel\Renderer\Artefact;
use phpDocumentor\DomainModel\Renderer\Artefacts;

final class FlySystemArtefacts implements Artefacts
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
     * @inheritdoc
     */
    public function persist(Artefact $artefact)
    {
        $this->filesystem->put($artefact->location(), $artefact->content());
    }
}
