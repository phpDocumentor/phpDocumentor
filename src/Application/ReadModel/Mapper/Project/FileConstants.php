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

use phpDocumentor\DomainModel\ReadModel\Mapper\Project\Interpret as InterpretInterface;
use phpDocumentor\Reflection\Php\File as FileType;

final class FileConstants extends AbstractReducer
{
    public function __invoke(InterpretInterface $command, $state)
    {
        if (!$command->subject() instanceof FileType) {
            return $command->interpreter()->next($command, $state);
        }

        $file = $command->subject();

        $state['constants'] = $this->convertItems(
            $file->getConstants(),
            $command->interpreter(),
            $command->context()
        );

        $state = $command->interpreter()->next($command, $state);

        return $state;
    }
}
