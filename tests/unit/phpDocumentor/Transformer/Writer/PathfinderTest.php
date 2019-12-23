<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Pathfinder
 * @covers ::<private>
 */
final class PathfinderTest extends TestCase
{
    /**
     * @dataProvider provideVariousQueriesToTestWith
     * @covers ::find
     */
    public function testThatFindingAResultWithAQuery($query, $expected) : void
    {
        $pathfinder = new Pathfinder();

        $anObjectHierarchy = $this->givenAnObjectHierarchy();
        $result = $pathfinder->find($anObjectHierarchy, $query);

        $this->assertEquals($expected, $result);
    }

    public function provideVariousQueriesToTestWith() : array
    {
        $obj = $this->givenAnObjectHierarchy();

        return [
            'empty query returns whole object, wrapped in an array' => ['', [$obj]],
            'query returns top-level property' => ['exampleArray', $obj->exampleArray],
            'query returns nested property, wrapped in an array' => ['nested.value', [$obj->nested->value]],
            'query returns nested magic property, wrapped in an array' => ['nested.magic', [$obj->nested->magic]],
            'query returns element from array in child, wrapped in an array' => [
                'exampleArray.key1',
                [$obj->exampleArray['key1']],
            ],
            'query returns top-level method response, wrapped in an array' => [
                'exampleMethod',
                [$obj->exampleMethod()],
            ],
            'query can omit "get" prefix for a method' => ['exampleGetter', [$obj->getExampleGetter()]],
            'query can omit "is" prefix for a conditional method' => [
                'exampleConditional',
                [$obj->isExampleConditional()],
            ],

            // Not sure I agree with this nowadays, but still provided for BC
            'query silently fails on unknown key' => ['propertyDoesNotExist', [null]],
            'query silently fails on magic property returning null / not existing' => [
                'nested.propertyDoesNotExist',
                [null],
            ],

            // this may be an issue for magic properties that are meant to return false or an empty string; but BC
            'query silently fails on magic property returning false' => ['nested.falseProperty', [null]],
            'query silently fails on magic property returning empty string' => ['nested.emptyString', [null]],
        ];
    }

    private function givenAnObjectHierarchy() : object
    {
        $topLevel = new class() {
            /** @var array */
            public $exampleArray = ['key1' => 'value1'];

            /** @var object */
            public $nested;

            public function __construct()
            {
                $this->nested = new class () {
                    /** @var string */
                    public $value = 'nestedValue';

                    /**
                     * @return string|bool|null
                     */
                    public function __get($name)
                    {
                        if ($name === 'emptyString') {
                            return '';
                        }
                        if ($name === 'magic') {
                            return 'value';
                        }
                        if ($name === 'falseProperty') {
                            return false;
                        }

                        return null;
                    }
                };
            }

            public function exampleMethod() : string
            {
                return 'valueFromMethod';
            }

            public function getExampleGetter() : string
            {
                return 'valueFromGetter';
            }

            public function isExampleConditional() : bool
            {
                return false;
            }
        };

        return $topLevel;
    }
}
