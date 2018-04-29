<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Created by PhpStorm.
 * User: otterdijk
 * Date: 4/21/18
 * Time: 11:06 PM
 */

namespace phpDocumentor\Application;

use League\Pipeline\PipelineBuilder;

final class PipelineFactory
{
    public static function create(...$stages): \League\Pipeline\PipelineInterface
    {
        $builder = new PipelineBuilder();
        foreach ($stages as $stage) {
            $builder->add($stage);
        }

        return $builder->build();
    }
}
