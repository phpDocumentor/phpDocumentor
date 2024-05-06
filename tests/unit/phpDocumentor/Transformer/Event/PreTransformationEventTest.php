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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the functionality for the PreTransformationEvent class.
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Event\PreTransformationEvent
 */
final class PreTransformationEventTest extends TestCase
{
    use Faker;

    public function testSetAndGetTransformation(): void
    {
        $transformation = self::faker()->transformation();
        $subject = new stdClass();

        $fixture = PreTransformationEvent::create($subject, $transformation);

        $this->assertSame($transformation, $fixture->getTransformation());
        $this->assertSame($subject, $fixture->getSubject());
    }
}
