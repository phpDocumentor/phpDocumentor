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

namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\ReadModel\ReadModels as ReadModels;

class RenderContext
{
    /** @var ReadModels */
    private $readModels;

    /** @var Assets */
    private $assets;

    /** @var Artefacts */
    private $artefacts;

    /**
     * @param ReadModels $readModels
     * @param Assets $assets
     * @param Artefacts $artefacts
     */
    public function __construct(ReadModels $readModels, Assets $assets, Artefacts $artefacts)
    {
        $this->readModels = $readModels;
        $this->assets = $assets;
        $this->artefacts = $artefacts;
    }

    /**
     * @return ReadModels
     */
    public function readModels()
    {
        return $this->readModels;
    }

    /**
     * @return Assets
     */
    public function assets()
    {
        return $this->assets;
    }

    /**
     * @return Artefacts
     */
    public function artefacts()
    {
        return $this->artefacts;
    }
}
