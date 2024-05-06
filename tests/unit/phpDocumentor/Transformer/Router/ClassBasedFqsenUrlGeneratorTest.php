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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Test for the MethodDescriptor URL Generator with the Standard Router
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Router\ClassBasedFqsenUrlGenerator
 * @covers ::__construct
 * @covers ::<private>
 */
class ClassBasedFqsenUrlGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::__invoke
     * @dataProvider provideFqsens
     */
    public function testGenerateUrlForFqsenDescriptor(string $fromFqsen, string $toPath): void
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator->generate(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($toPath);

        $fqsen = new Fqsen($fromFqsen);
        $fixture = new ClassBasedFqsenUrlGenerator($urlGenerator->reveal(), new AsciiSlugger());

        $result = $fixture($fqsen);

        $this->assertSame($toPath, $result);
    }

    public static function provideFqsens(): array
    {
        return [
            ['\\My\\Space\\Class', '/classes/My-Space-Class-html'],
            ['\\My\\Space\\Class::$property', '/classes/My-Space-Class-html#property_property'],
            ['\\My\\Space\\Class::method()', '/classes/My-Space-Class-html#method_method'],
            ['\\My\\Space\\Class::CONSTANT', '/classes/My-Space-Class-html#constant_CONSTANT'],
        ];
    }
}
