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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template\Collection
 * @covers ::<private>
 */
final class CollectionTest extends MockeryTestCase
{
    use Faker;

    /** @var Collection */
    private $fixture;

    /**
     * Constructs the fixture with provided mocked dependencies.
     */
    protected function setUp(): void
    {
        $this->fixture = new Collection([]);
    }

    /**
     * @covers ::getTransformations
     */
    public function testIfAllTransformationsCanBeRetrieved(): void
    {
        $transformation1 = $this->givenAnEmptyTransformation();
        $transformation2 = $this->givenAnEmptyTransformation();
        $transformation3 = $this->givenAnEmptyTransformation();
        $this->whenThereIsATemplateWithNameAndTransformations(
            'template1',
            ['a' => $transformation1, 'b' => $transformation2]
        );
        $this->whenThereIsATemplateWithNameAndTransformations('template2', ['c' => $transformation3]);

        $result = $this->fixture->getTransformations();

        $this->assertCount(3, $result);
        $this->assertSame([$transformation1, $transformation2, $transformation3], $result);
    }

    /**
     * Returns a transformation object without information in it.
     */
    private function givenAnEmptyTransformation(?Template $template = null): Transformation
    {
        return $this->faker()->transformation($template);
    }

    /**
     * Adds a template to the fixture with the given name and transformations.
     *
     * @param Transformation[] $transformations
     */
    private function whenThereIsATemplateWithNameAndTransformations(string $name, array $transformations): void
    {
        $template = $this->faker()->template($name);
        foreach ($transformations as $key => $transformation) {
            $template[$key] = $transformation;
        }

        $this->fixture[$name] = $template;
    }
}
