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

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Documentation;

final class Render
{
    /** @var string */
    private $target;

    /** @var string[] */
    private $templates;

    /** @var Documentation */
    private $documentation;

    /**
     * @param          $target
     * @param string[] $templates array of templates to render.
     */
    public function __construct($documentation, $target, array $templates)
    {
        $this->target    = $target;
        $this->templates = $templates;
        $this->documentation = $documentation;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    public function getDocumentation()
    {
        return $this->documentation;
    }
}
