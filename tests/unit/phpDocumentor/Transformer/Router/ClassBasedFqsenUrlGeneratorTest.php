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

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Fqsen;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Test for the MethodDescriptor URL Generator with the Standard Router
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Router\ClassBasedFqsenUrlGenerator
 * @covers ::__construct
 * @covers ::<private>
 */
class ClassBasedFqsenUrlGeneratorTest extends MockeryTestCase
{
    /**
     * @covers ::__invoke
     * @dataProvider provideFqsens
     */
    public function testGenerateUrlForFqsenDescriptor(string $fromFqsen, string $toPath) : void
    {
        // Arrange
        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $urlGenerator->shouldReceive('generate')->andReturn($toPath);
        $fqsen = new Fqsen($fromFqsen);
        $fixture = new ClassBasedFqsenUrlGenerator($urlGenerator);

        // Act
        $result = $fixture($fqsen);

        // Assert
        $this->assertSame($toPath, $result);
    }

    public function provideFqsens() : array
    {
        return [
            ['\\My\\Space\\Class', '/classes/My-Space-Class-html'],
            ['\\My\\Space\\Class::$property', '/classes/My-Space-Class-html#property_property'],
            ['\\My\\Space\\Class::method()', '/classes/My-Space-Class-html#method_method'],
            ['\\My\\Space\\Class::CONSTANT', '/classes/My-Space-Class-html#constant_CONSTANT'],
        ];
    }
}
