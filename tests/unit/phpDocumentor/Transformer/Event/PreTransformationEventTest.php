<?php
declare(strict_types=1);

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
 */

namespace phpDocumentor\Transformer\Event;
use phpDocumentor\Transformer\Transformation;

/**
 * Tests the functionality for the PreTransformationEvent class.
 */
final class PreTransformationEventTest extends \PHPUnit\Framework\TestCase
{
    /** @var PreTransformationEvent $fixture */
    protected $fixture;

    /** @var \DOMDocument */
    protected $source;

    /** @var Transformation */
    private $transformation;

    protected function setUp()
    {
        $this->fixture = new PreTransformationEvent(new \stdClass());
        $this->source = new \DOMDocument('1.0', 'UTF-8');
        $this->transformation = new Transformation('', '', '', '');
    }

    /**
     * @covers \phpDocumentor\Transformer\Event\PreTransformationEvent::getSource
     * @covers \phpDocumentor\Transformer\Event\PreTransformationEvent::setSource
     */
    public function testSetAndGetSource()
    {
        $this->assertNull($this->fixture->getSource());

        $this->fixture->setSource($this->source);

        $this->assertSame($this->source, $this->fixture->getSource());
    }

    /**
     * @covers \phpDocumentor\Transformer\Event\PreTransformationEvent::getTransformation
     * @covers \phpDocumentor\Transformer\Event\PreTransformationEvent::setTransformation
     */
    public function testSetAndGetTransformation()
    {
        $this->assertNull($this->fixture->getTransformation());

        $this->fixture->setTransformation($this->transformation);

        $this->assertSame($this->transformation, $this->fixture->getTransformation());
    }
}
