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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Transformer\Transformation;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the functionality for the PreTransformationEvent class.
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Event\PreTransformationEvent
 * @covers ::<private>
 */
final class PreTransformationEventTest extends TestCase
{
    /** @var PreTransformationEvent $fixture */
    protected $fixture;

    /** @var Transformation */
    private $transformation;

    /**
     * @covers ::getTransformation
     * @covers ::getSubject
     * @covers ::create
     */
    public function testSetAndGetTransformation() : void
    {
        $this->transformation = new Transformation('', '', '', '');
        $subject              = new stdClass();
        $this->fixture        = PreTransformationEvent::create($subject, $this->transformation);
        $this->assertSame($this->transformation, $this->fixture->getTransformation());
        $this->assertSame($subject, $this->fixture->getSubject());
    }
}
