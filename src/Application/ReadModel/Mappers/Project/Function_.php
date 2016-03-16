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

namespace phpDocumentor\Application\ReadModel\Mappers\Project;

use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Reducer;
use phpDocumentor\Reflection\InterpretInterface;
use phpDocumentor\Reflection\Php\Function_ as FunctionType;

final class Function_ extends AbstractReducer implements Reducer
{
    public function __invoke(InterpretInterface $command, $state)
    {
        if (!$command->subject() instanceof FunctionType) {
            return $command->interpreter()->next($command, $state);
        }

        $newState = ['function'];

        return $newState;
    }
}
