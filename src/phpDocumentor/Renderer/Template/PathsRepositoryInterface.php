<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Renderer\Template;

use phpDocumentor\Path;
use phpDocumentor\Renderer\Template;

interface PathsRepositoryInterface
{
    /**
     * Lists the folders where templates can be found
     *
     * @param Template|null $template
     * @return string[]
     */
    public function listLocations(Template $template = null);

    /**
     * Finds a template and returns the full name and path of the view
     *
     * @param Template $template
     * @param Path $view
     * @return null|Path
     */
    public function findByTemplateAndPath(Template $template, Path $view);

    /**
     * Lists all available templates
     *
     * @return string[]
     */
    public function listTemplates();
}