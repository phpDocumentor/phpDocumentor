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

use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Transformer\Transformer;

final class TransformHandler
{
    /** @var Compiler */
    private $compiler;

    /** @var Transformer */
    private $transformer;

    /** @var Analyzer */
    private $analyzer;

    public function __construct(Transformer $transformer, Compiler $compiler, Analyzer $analyzer)
    {
        $this->compiler    = $compiler;
        $this->transformer = $transformer;
        $this->analyzer = $analyzer;
    }

    public function __invoke(Transform $command)
    {
        $this->transformer->setTarget($command->getTarget());

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $pass->execute($this->analyzer->getProjectDescriptor());
        }
    }
}
