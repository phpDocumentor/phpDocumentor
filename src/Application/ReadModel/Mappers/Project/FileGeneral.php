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
use phpDocumentor\Reflection\Php\File as FileType;

final class FileGeneral implements Reducer
{
    public function __invoke(InterpretInterface $command, $state)
    {
        if (!$command->subject() instanceof FileType) {
            return $command->interpreter()->next($command, $state);
        }

        $file = $command->subject();
        if ($file->getDocBlock() && $file->getDocBlock()->getDescription()) {
            $description = (string) $file->getDocBlock()->getDescription();
        } else {
            $description = '';
        }

        $newState = [
            'hash' => $file->getHash(),
            'path' => $file->getPath(),
            'source' => $file->getSource(),
            'namespaceAliases' => $command->context()->getNamespaceAliases(),
            'includes' => $file->getIncludes(),
            'markers' => [],
            'fqsen' => '',
            'name' => $file->getName(),
            'namespace' => $command->context()->getNamespace(),
            'package' => '',
            'summary' => $file->getDocBlock() ? $file->getDocBlock()->getSummary() : '',
            'description' => $description,
            'filedescriptor' => null,
            'line' => 0,
            'errors' => '',
            'inheritedElement' => null
        ];

        $newState = $command->interpreter()->next($command, $newState);

        return $newState;
    }
}
