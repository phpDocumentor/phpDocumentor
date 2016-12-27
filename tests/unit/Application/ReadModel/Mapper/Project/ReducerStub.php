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

final class ReducerStub extends AbstractReducer
{
    public $isCalled = 0;

    public function __invoke(InterpretInterface $command, $state)
    {
        $this->isCalled += 1;

        return $state;
    }
}
