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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen as FqsenReference;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the SeeDescriptor class.
 */
class SeeDescriptorTest extends TestCase
{
    public function testSetAndGetReference(): void
    {
        $refrence = new FqsenReference(new Fqsen('\someFunction()'));
        $fixture = new SeeDescriptor('name', $refrence, null);
        $result = $fixture->getReference();

        $this->assertSame($refrence, $result);
    }
}
