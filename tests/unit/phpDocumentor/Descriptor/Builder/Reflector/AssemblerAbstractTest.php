<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class AssemblerAbstractTest extends TestCase
{
    /**
     * @param Type|null $type
     * @param string $expected
     * @dataProvider typeProvider
     */
    public function testDeduplicateTypes(?Type $type, string $expected)
    {
        $type = AssemblerAbstract::deduplicateTypes($type);

        self::assertEquals($expected, (string)$type);
    }

    public function typeProvider()
    {
        return [
            [
                new Compound([new String_(), new Integer()]),
                'string|int'
            ],
            [
                new Compound([new String_(), new String_()]),
                'string'
            ],
            [
                new String_(),
                'string'
            ],

        ];
    }
}
