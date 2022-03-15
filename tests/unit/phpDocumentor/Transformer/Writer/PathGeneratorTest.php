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

namespace phpDocumentor\Transformer\Writer;

use League\Flysystem\MountManager;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;

/**
 * @uses \phpDocumentor\Transformer\Writer\Pathfinder
 * @uses \phpDocumentor\Transformer\Template
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\PathGenerator
 * @covers ::__construct
 * @covers ::<private>
 */
final class PathGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Router */
    private $router;

    /** @var PathGenerator */
    private $generator;

    /** @var Template */
    private $template;

    protected function setUp(): void
    {
        $this->template = new Template('My Template', new MountManager());

        $this->router = $this->prophesize(Router::class);
        $engine = $this->prophesize(Engine::class);
        //phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic
        $engine->perform(Argument::type(FileDescriptor::class), Argument::any())->will(function ($arguments) {
            return $arguments[0]->getPath();
        });

        $this->generator = new PathGenerator(
            $this->router->reveal(),
            $engine->reveal()
        );
    }

    /**
     * @covers ::generate
     * @dataProvider providePathsToUrlEncode
     */
    public function testWhenAnArtifactIsProvidedGenerateAPathToThatLocation($artifact, $variable, $expected): void
    {
        $transformation = $this->givenATransformationWithArtifact($artifact);

        $descriptor = new FileDescriptor('hash');
        $descriptor->setPath($variable);
        $path = $this->generator->generate($descriptor, $transformation);

        $this->assertSame($expected, $path);
    }

    /**
     * @covers ::generate
     */
    public function testAnErrorOccursWhenAnUnknownVariableIsAsked(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Variable substitution in path /file/{{a}} failed, variable "a" did not return a value'
        );
        $transformation = $this->givenATransformationWithArtifact('file/{{a}}');

        $this->generator->generate(new FileDescriptor('hash'), $transformation);
    }

    /**
     * @covers ::generate
     */
    public function testAnErrorOccursWhenAnEmptyVariableIsAsked(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Variable substitution in path /file/{{}} failed, no variable was specified'
        );
        $transformation = $this->givenATransformationWithArtifact('file/{{}}');

        $this->generator->generate(new FileDescriptor('hash'), $transformation);
    }

    private function givenATransformationWithArtifact(string $artifact): Transformation
    {
        return new Transformation(
            $this->template,
            '',
            'twig',
            'templates/templateName/index.html.twig',
            $artifact
        );
    }

    public function providePathsToUrlEncode(): array
    {
        return [
            'without variables' => [
                'file/index.html',
                'thisIsAFile.php',
                '/file/index.html',
            ],
            'normal path variable' => [
                'file/{{path}}',
                'thisIsAFile.php',
                '/file/thisIsAFile.php',
            ],
            'transliterates from unicode to ascii' => [
                'file/{{path}}',
                'thisIsд.php',
                '/file/thisIsd.php',
            ],
            'removes a leading and trailing directory separators' => [
                'file/{{path}}',
                '/thisIsAFile.php\\',
                '/file/thisIsAFile.php',
            ],
            'removes whitespace' => [
                'file/{{path}}',
                ' thisIsAFile.php ',
                '/file/thisIsAFile.php',
            ],
            'without directory separator all is encoded' => [
                'file/{{path}}',
                'this Is A (File).php',
                '/file/this+Is+A+%28File%29.php',
            ],
            'with unix separator, each part is encoded' => [
                'file/{{path}}',
                'directory/this Is A (File).php',
                '/file/directory/this+Is+A+%28File%29.php',
            ],
            'with windows separator, each part is encoded' => [
                'file/{{path}}',
                'directory\\this Is A (File).php',
                '/file/directory\\this+Is+A+%28File%29.php',
            ],
            'with windows and unix separator, each unix part is encoded' => [
                'file/{{path}}',
                'directory/this Is\\A (File).php',
                '/file/directory/this+Is%5CA+%28File%29.php',
            ],
        ];
    }
}
