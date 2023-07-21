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

namespace phpDocumentor\Transformer;

use ArrayIterator;
use InvalidArgumentException;
use League\Flysystem\MountManager;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template\Parameter;
use PHPUnit\Framework\TestCase;

use function count;
use function iterator_to_array;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template
 * @covers ::__construct
 * @covers ::<private>
 */
final class TemplateTest extends TestCase
{
    use Faker;

    /**
     * @covers ::getName
     * @covers ::getAuthor
     * @covers ::setAuthor
     * @covers ::getDescription
     * @covers ::setDescription
     * @covers ::getCopyright
     * @covers ::setCopyright
     * @covers ::getParameters
     * @covers ::setParameter
     * @covers ::getVersion
     * @covers ::setVersion
     * @covers ::offsetSet
     * @covers ::offsetGet
     * @covers ::files
     */
    public function testConstructingATemplateWithAllProperties(): void
    {
        $parameter = new Parameter('key', 'value');
        $files = $this->givenExampleMountManager();

        $template = new Template('name', $files);
        $template->setAuthor('Mike');
        $template->setCopyright('copyright');
        $template->setDescription('description');
        $template->setParameter('key', $parameter);
        $template->setVersion('1.0.0');

        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;

        $this->assertSame('name', $template->getName());
        $this->assertSame('Mike', $template->getAuthor());
        $this->assertSame('copyright', $template->getCopyright());
        $this->assertSame('description', $template->getDescription());
        $this->assertSame($parameter, $template->getParameters()['key']);
        $this->assertSame('1.0.0', $template->getVersion());
        $this->assertSame($files, $template->files());
        $this->assertSame($transformation, $template['key']);
    }

    /** @covers ::offsetSet */
    public function testThatArrayElementsMayOnlyBeTransformations(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $template = new Template('name', $this->givenExampleMountManager());
        $template['key'] = 'value';
    }

    /** @covers ::setVersion */
    public function testThatVersionsAreRejectedIfTheyDontMatchNumbersSeparatedByDots(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $template = new Template('name', $this->givenExampleMountManager());
        $template->setVersion('abc');
    }

    /** @covers ::offsetGet */
    public function testThatWeCanCheckIfATransformationIsRegistered(): void
    {
        $template = new Template('name', $this->givenExampleMountManager());
        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;

        $this->assertTrue(isset($template['key']));
        $this->assertFalse(isset($template['not_key']));
    }

    /**
     * @covers ::offsetExists
     * @covers ::offsetUnset
     */
    public function testThatWeCanUnsetATransformation(): void
    {
        $template = new Template('name', $this->givenExampleMountManager());
        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;

        $this->assertTrue(isset($template['key']));

        unset($template['key']);

        $this->assertFalse(isset($template['key']));
    }

    /** @covers ::count */
    public function testThatWeCanCountTheNumberOfTransformations(): void
    {
        $template = new Template('name', $this->givenExampleMountManager());
        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;

        $this->assertSame(1, count($template));
    }

    /** @covers ::getIterator */
    public function testThatWeCanIterateOnTheTransformations(): void
    {
        $template = new Template('name', $this->givenExampleMountManager());
        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;

        $this->assertInstanceOf(ArrayIterator::class, $template->getIterator());
        $this->assertSame(['key' => $transformation], iterator_to_array($template));
    }

    /** @covers ::propagateParameters */
    public function testThatAllParametersArePropagatedToTheTransformationsWhenNeeded(): void
    {
        $parameter = new Parameter('key', 'value');

        $template = new Template('name', $this->givenExampleMountManager());
        $transformation = new Transformation($template, '', '', '', '');
        $template['key'] = $transformation;
        $template->setParameter('key', $parameter);

        $template->propagateParameters();

        $this->assertSame(['key' => $parameter], $transformation->getParameters());
    }

    private function givenExampleMountManager(): MountManager
    {
        return new MountManager();
    }
}
