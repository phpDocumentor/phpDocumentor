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

namespace phpDocumentor\Descriptor\ValueObjects;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass IsApplicable
 * @covers ::__construct
 * @covers ::<private>
 */
final class IsApplicableTest extends TestCase
{
    /**
     * @covers ::true()
     * @covers ::isTrue()
     * @covers ::isFalse()
     */
    public function testIndicatingPresenceOfFeature(): void
    {
        $featureToggle = IsApplicable::true();
        self::assertTrue($featureToggle->isTrue());
        self::assertFalse($featureToggle->isFalse());
    }

    /**
     * @covers ::false()
     * @covers ::isTrue()
     * @covers ::isFalse()
     */
    public function testIndicatingAbsenceOfFeature(): void
    {
        $featureToggle = IsApplicable::false();
        self::assertFalse($featureToggle->isTrue());
        self::assertTrue($featureToggle->isFalse());
    }

    /**
     * @covers ::inverse()
     * @covers ::isTrue()
     * @covers ::isFalse()
     */
    public function testValueCanBeInvertedAndIsImmutable(): void
    {
        $original = IsApplicable::false();
        $changed = $original->inverse();

        self::assertNotSame($original, $changed);
        self::assertFalse($changed->isFalse());
        self::assertTrue($changed->isTrue());
    }

    /**
     * @covers ::fromBoolean
     */
    public function testCanBeInstantiatedFromBoolean(): void
    {
        self::assertTrue(IsApplicable::fromBoolean(true)->isTrue());
        self::assertTrue(IsApplicable::fromBoolean(false)->isFalse());
    }

    /**
     * @covers ::equals
     */
    public function testCanBeComparedToAnother(): void
    {
        self::assertTrue(IsApplicable::true()->equals(IsApplicable::true()));
        self::assertFalse(IsApplicable::true()->equals(IsApplicable::false()));
        self::assertTrue(IsApplicable::false()->equals(IsApplicable::false()));
        self::assertFalse(IsApplicable::false()->equals(IsApplicable::true()));
    }
}
