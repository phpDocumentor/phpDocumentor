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

final class File extends AbstractReducer implements Reducer
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

        if ($file->getDocBlock() && $file->getDocBlock()->getTags()) {
            $tags = $file->getDocBlock()->getTags();
        } else {
            $tags = [];
        }

        $newState = [
            'hash' => $file->getHash(),
            'path' => $file->getPath(),
            'source' => $file->getSource(),
            'namespaceAliases' => $command->context()->getNamespaceAliases(),
            'includes' => $file->getIncludes(),
            'constants' => $this->convertItems(
                $file->getConstants(),
                $command->interpreter(),
                $command->context()
            ),
            'functions' => $this->convertItems(
                $file->getFunctions(),
                $command->interpreter(),
                $command->context()
            ),
            'classes' => $this->convertItems(
                $file->getClasses(),
                $command->interpreter(),
                $command->context()
            ),
            'interfaces' => $this->convertItems(
                $file->getInterfaces(),
                $command->interpreter(),
                $command->context()
            ),
            'traits' => $this->convertItems(
                $file->getTraits(),
                $command->interpreter(),
                $command->context()
            ),
            'markers' => [],
            'fqsen' => '',
            'name' => $file->getName(),
            'namespace' => $command->context()->getNamespace(),
            'package' => '',
            'summary' => $file->getDocBlock() ? $file->getDocBlock()->getSummary() : '',
            'description' => $description,
            'filedescriptor' => null,
            'line' => 0,
            'tags' => $this->convertItems(
                $tags,
                $command->interpreter(),
                $command->context()
            ),
            'errors' => '',
            'inheritedElement' => null

        ];

        return $newState;
    }

    private function convertItems($items, $interpreter, $context)
    {
        $convertedItems = [];
        foreach ($items as $item) {
            $convertedItems[$item->getName()] = $this->convertItem($item, $interpreter, $context);
        }
        return $convertedItems;
    }
}
