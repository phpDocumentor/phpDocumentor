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

namespace phpDocumentor\Transformer;

use phpDocumentor\Transformer\Template\Parameter;

/**
 * Test class for \phpDocumentor\Transformer\Transformer.
 *
 * @covers \phpDocumentor\Transformer\Template
 */
final class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructingATemplateWithAllProperties()
    {
        $parameter = new Parameter();
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

    public function testThatArrayElementsMayOnlyBeTransformations()
    {
        $this->expectException(\InvalidArgumentException::class);

        $template = new Template('name');
        $template['key'] = 'value';
    }

    public function testThatVersionsAreRejectedIfTheyDontMatchNumbersSeparatedByDots()
    {
        $this->expectException(\InvalidArgumentException::class);

        $template = new Template('name');
        $template->setVersion('abc');
    }

    public function testThatWeCanCheckIfATransformationIsRegistered()
    {
        $template = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertTrue(isset($template['key']));
        $this->assertFalse(isset($template['not_key']));
    }

    public function testThatWeCanUnsetATransformation()
    {
        $template = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertTrue(isset($template['key']));

        unset($template['key']);

        $this->assertFalse(isset($template['key']));
    }

    public function testThatWeCanCountTheNumberOfTransformations()
    {
        $template = new Template('name');
        $template['key'] = new Transformation('', '', '', '');

        $this->assertSame(1, count($template));
    }

    public function testThatWeCanIterateOnTheTransformations()
    {
        $template = new Template('name');
        $transformation = new Transformation('', '', '', '');
        $template['key'] = $transformation;

        $this->assertInstanceOf(\ArrayIterator::class, $template->getIterator());
        $this->assertSame(['key' => $transformation], iterator_to_array($template));
    }

    public function testThatAllParametersArePropagatedToTheTransformationsWhenNeeded()
    {
        $parameter = new Parameter();
        $transformation = new Transformation('', '', '', '');

        $template = new Template('name');
        $template['key'] = $transformation;
        $template->setParameter('key', $parameter);

        $template->propagateParameters();

        $this->assertSame(['key' => $parameter], $transformation->getParameters());
    }
}
