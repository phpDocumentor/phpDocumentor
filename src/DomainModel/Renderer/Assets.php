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

use phpDocumentor\DomainModel\Path;

/**
 * Classes consuming this interface should be able to locate and return assets when a location is provided.
 */
interface Assets
{
    /**
     * Returns a new instance of this collection with the assets for the given template included.
     *
     * @param Template $template
     *
     * @return Assets
     */
    public function includeTemplateAssets(Template $template);

    /**
     * Checks whether the asset with the given location can be found.
     *
     * @param Path $location
     *
     * @return boolean
     */
    public function has(Path $location);

    /**
     * Retrieves the asset with the given name.
     *
     * @param Path $location a location in one of the asset locations.
     *
     * @throws AssetNotFoundException if no asset could be found at that location.
     *
     * @return Asset
     */
    public function get(Path $location);
}
