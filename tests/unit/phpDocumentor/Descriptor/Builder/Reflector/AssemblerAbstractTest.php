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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

class AssemblerAbstractTest extends TestCase
{
    /** @dataProvider typeProvider */
    public function testDeduplicateTypes(Type|null $type, string $expected): void
    {
        $type = AssemblerAbstract::deduplicateTypes($type);

        self::assertEquals($expected, (string) $type);
    }

    public static function typeProvider(): array
    {
        return [
            [
                new Compound([new String_(), new Integer()]),
                'string|int',
            ],
            [
                new Compound([new String_(), new String_()]),
                'string',
            ],
            [
                new String_(),
                'string',
            ],

        ];
    }
}
