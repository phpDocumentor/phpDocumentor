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

namespace phpDocumentor\Transformer;

use ArrayIterator;
use InvalidArgumentException;
use phpDocumentor\Transformer\Template\Parameter;
use PHPUnit\Framework\TestCase;
use function count;
use function iterator_to_array;

/**
 * Test class for \phpDocumentor\Transformer\Transformer.
 *
 * @covers \phpDocumentor\Transformer\Template
 */
final class TemplateTest extends TestCase
{
    public function testConstructingATemplateWithAllProperties() : void
    {
        $parameter      = new Parameter();
        $transformation = new Transformation('', '', '', '');

        $template = new Template('name');
        $template->setAuthor('Mike');
        $template->setCopyright('copyright');
        $template->setDescription('description');
        $template->setParameter('key', $parameter);
        $template->setVersion('1.0.0');
        $template['key'] = $transformation;

        $this->assertSame('name', $template->getName());
        $this->assertSame('Mike', $template->getAuthor());
        $this->assertSame('copyright', $template->getCopyright());
        $this->assertSame('description', $template->getDescription());
        $this->assertSame($parameter, $template->getParameters()['key']);
        $this->assertSame('1.0.0', $template->getVersion());
        $this->assertSame($transformation, $template['key']);
    }

    public function testThatArrayElementsMayOnlyBeTransformations() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $template        = new Template('name');
        $template['key'] = 'value';
    }

    public function testThatVersionsAreRejectedIfTheyDontMatchNumbersSeparatedByDots() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $template = new Template('name');
        $template->setVersion('abc');
    }

    public function testThatWeCanCheckIfATransformationIsRegistered() : void
    {
        $template        = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertTrue(isset($template['key']));
        $this->assertFalse(isset($template['not_key']));
    }

    public function testThatWeCanUnsetATransformation() : void
    {
        $template        = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertTrue(isset($template['key']));

        unset($template['key']);

        $this->assertFalse(isset($template['key']));
    }

    public function testThatWeCanCountTheNumberOfTransformations() : void
    {
        $template        = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertSame(1, count($template));
    }

    public function testThatWeCanIterateOnTheTransformations() : void
    {
        $template        = new Template('name');
        $transformation  = new Transformation('', '', '', '');
        $template['key'] = $transformation;

        $this->assertInstanceOf(ArrayIterator::class, $template->getIterator());
        $this->assertSame(['key' => $transformation], iterator_to_array($template));
    }

    public function testThatAllParametersArePropagatedToTheTransformationsWhenNeeded() : void
    {
        $parameter      = new Parameter();
        $transformation = new Transformation('', '', '', '');

        $template        = new Template('name');
        $template['key'] = $transformation;
        $template->setParameter('key', $parameter);

        $template->propagateParameters();

        $this->assertSame(['key' => $parameter], $transformation->getParameters());
    }
}
