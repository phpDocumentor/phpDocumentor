<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 *
 */

namespace phpDocumentor\Application\Transformer\Template;

use phpDocumentor\Transformer\Template\PathResolver;

final class PathResolverFactory
{
    public static function create(): PathResolver
    {
        $templateDir = __DIR__ . '/../../../../../data/templates';

        // when installed using composer the templates are in a different folder
        $composerTemplatePath = __DIR__ . '/../../../../../../templates';
        if (file_exists($composerTemplatePath)) {
            $templateDir = $composerTemplatePath;
        }

        return new PathResolver($templateDir);
    }
}
