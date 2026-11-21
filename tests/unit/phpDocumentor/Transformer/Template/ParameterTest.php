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

namespace phpDocumentor\Transformer\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parameter::class)]
final class ParameterTest extends TestCase
{
    public function testGetKey(): void
    {
        $fixture = new Parameter('key', 'value');

        $this->assertSame('key', $fixture->key());
    }

    public function testGetValue(): void
    {
        $fixture = new Parameter('key', 'value');

        $this->assertSame('value', $fixture->value());
    }
}
