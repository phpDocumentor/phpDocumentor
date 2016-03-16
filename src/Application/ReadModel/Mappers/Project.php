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

namespace phpDocumentor\Application\ReadModel\Mappers;

use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\ReadModel\Mapper;
use phpDocumentor\DomainModel\ReadModel\Definition;
use phpDocumentor\Reflection\Interpret;
use phpDocumentor\Reflection\Interpreter;
use phpDocumentor\Reflection\Types\Context;

class Project implements Mapper
{

    /**
     * @var Interpreter
     */
    private $interpreter;

    public function __construct(Interpreter $interpreter)
    {

        $this->interpreter = $interpreter;
    }

    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param Definition $readModelDefinition
     * @param Documentation $documentation
     *
     * @return mixed
     */
    public function create(Definition $readModelDefinition, Documentation $documentation)
    {
        $projectDescriptor = ['files' => []];
        $project = $documentation->getDocumentGroups()[0]->getProject();
        $files = $project->getFiles();

        foreach ($files as $file) {
            if ($file->getDocBlock() && $file->getDocBlock()->getContext()) {
                $context = $file->getDocBlock()->getContext();
            } else {
                $context = new Context("");
            }
            $projectDescriptor['files'][$file->getName()] = $this->convertItem($file, $context);
        }

        return $projectDescriptor;
    }

    private function convertItem($item, $context, $state = null)
    {
        $command = new Interpret($item, $context);
        $interpreter = clone $this->interpreter;
        return $interpreter->interpret($command, $state);
    }
}
