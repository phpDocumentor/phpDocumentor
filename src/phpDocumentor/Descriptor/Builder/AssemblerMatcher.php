<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2019 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Descriptor\Builder;

final class AssemblerMatcher
{
    /**
     * @var callable
     */
    private $matcher;

    /**
     * @var AssemblerInterface
     */
    private $assembler;

    public function __construct(callable $matcher, AssemblerInterface $assembler)
    {
        $this->matcher = $matcher;
        $this->assembler = $assembler;
    }

    public function match($criteria): bool
    {
        $matcher = $this->matcher;
        return $matcher($criteria);
    }

    public function getAssembler(): AssemblerInterface
    {
        return $this->assembler;
    }
}
