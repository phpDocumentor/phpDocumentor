<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Configuration\ApiSpecification */
final class ApiSpecificationTest extends TestCase
{
    /**
     * @dataProvider visibilityProvider
     * @covers ::offsetSet
     * @covers ::isVisibilityAllowed
     * @covers ::calculateVisiblity
     */
    public function testVisibility(array $settings, array $expected)
    {
        $apiSpec = ApiSpecification::createDefault();
        $apiSpec['visibility'] = $settings;

        foreach ($expected as $visiblity => $result) {
            self::assertSame($result, $apiSpec->isVisibilityAllowed($visiblity));
        }
    }

    /** @return array<array<string[], int>> */
    public static function visibilityProvider(): array
    {
        return [
            [
                'settings' => ['public'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => true,
                    ApiSpecification::VISIBILITY_PROTECTED => false,
                    ApiSpecification::VISIBILITY_PRIVATE => false,
                    ApiSpecification::VISIBILITY_INTERNAL => false,
                ],
            ],
            [
                'settings' => ['protected'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => false,
                    ApiSpecification::VISIBILITY_PROTECTED => true,
                    ApiSpecification::VISIBILITY_PRIVATE => false,
                    ApiSpecification::VISIBILITY_INTERNAL => false,
                ],
            ],
            [
                'settings' => ['private'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => false,
                    ApiSpecification::VISIBILITY_PROTECTED => false,
                    ApiSpecification::VISIBILITY_PRIVATE => true,
                    ApiSpecification::VISIBILITY_INTERNAL => false,
                ],
            ],
            [
                'settings' => ['public', 'private'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => true,
                    ApiSpecification::VISIBILITY_PROTECTED => false,
                    ApiSpecification::VISIBILITY_PRIVATE => true,
                    ApiSpecification::VISIBILITY_INTERNAL => false,
                ],
            ],
            [
                'settings' => ['public', 'internal'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => true,
                    ApiSpecification::VISIBILITY_PROTECTED => false,
                    ApiSpecification::VISIBILITY_PRIVATE => false,
                    ApiSpecification::VISIBILITY_INTERNAL => true,
                ],
            ],
            [
                'settings' => ['internal'],
                'expected' => [
                    ApiSpecification::VISIBILITY_PUBLIC => true,
                    ApiSpecification::VISIBILITY_PROTECTED => true,
                    ApiSpecification::VISIBILITY_PRIVATE => true,
                    ApiSpecification::VISIBILITY_INTERNAL => true,
                ],
            ],
        ];
    }
}
