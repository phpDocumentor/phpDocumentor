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

namespace phpDocumentor\Application\ReadModel\Mapper\Project;

use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Reducer;

abstract class AbstractReducer implements Reducer
{
    public function convertItems($items, $interpreter, $context)
    {
        $convertedItems = [];
        foreach ($items as $item) {
            $convertedItems[$item->getName()] = $this->convertItem($item, $interpreter, $context);
        }
        return $convertedItems;
    }

    public function convertItem($item, $interpreter, $context, $state = null)
    {
        $command = new Interpret($item, $context);
        $newInterpreter = clone $interpreter;
        return $newInterpreter->interpret($command, $state);
    }
}
