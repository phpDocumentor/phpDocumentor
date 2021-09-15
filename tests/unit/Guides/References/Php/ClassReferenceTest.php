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

namespace phpDocumentor\Guides\References\Php;

use PHPUnit\Framework\TestCase;

final class ClassReferenceTest extends TestCase
{
    public function testDomainNameAndRole(): void
    {
        $classReference = new ClassReference();

        self::assertSame('php', $classReference->getDomain());
        self::assertSame('class', $classReference->getName());
        self::assertSame('php:class', $classReference->getRole());
    }
}
