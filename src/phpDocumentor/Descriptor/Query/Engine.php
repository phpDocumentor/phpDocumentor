<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Query;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\JsonPath\Executor;
use phpDocumentor\JsonPath\Parser;

class Engine
{
    public function __construct(private readonly Executor $executor, private readonly Parser $parser)
    {
    }

    /** @return mixed */
    public function perform(Descriptor $descriptor, string $query)
    {
        return $this->executor->evaluate($this->parser->parse($query), $descriptor);
    }
}
