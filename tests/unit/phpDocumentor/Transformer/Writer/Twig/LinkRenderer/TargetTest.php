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

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testTitleIsPersistedInObject(): void
    {
        $target = new Target('title');

        self::assertSame('title', $target->getTitle());
    }

    public function testAUrlCanBeAssociatedWithATarget(): void
    {
        $target = new Target('title', 'url');

        self::assertSame('url', $target->getUrl());
    }

    public function testUrlsMayBeOmittedFromTargets(): void
    {
        $target = new Target('title');

        self::assertNull($target->getUrl());
    }

    public function testAPresentationStyleCanBeAssociatedWithATarget(): void
    {
        $target = new Target('title', 'url', LinkRenderer::PRESENTATION_CLASS_SHORT);

        self::assertSame(LinkRenderer::PRESENTATION_CLASS_SHORT, $target->getPresentation());
    }

    public function testDefaultPresentationStyleIsNormal(): void
    {
        $target = new Target('title');

        self::assertSame(LinkRenderer::PRESENTATION_NORMAL, $target->getPresentation());
    }

    /**
     * @dataProvider abbreviationPerTitleAndPresentationStyle
     */
    public function testAbbreviationIsDerivedFromTitleAndPresentation(
        string $title,
        string $presentation,
        ?string $expectedAbbreviation
    ): void {
        $target = new Target($title, null, $presentation);

        self::assertSame($expectedAbbreviation, $target->getAbbreviation());
    }

    /**
     * @return array<string, list<string|null>>
     */
    public function abbreviationPerTitleAndPresentationStyle(): array
    {
        return [
            'No abbreviation when there is no presentation style' => [
                'Pretty title',
                LinkRenderer::PRESENTATION_NONE,
                null,
            ],
            'No abbreviation for the URL presentation style' => [
                'Pretty title',
                LinkRenderer::PRESENTATION_URL,
                null,
            ],
            'No abbreviation for the class style when there is only one part' => [
                'MySuperClass',
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                null,
            ],
            'No abbreviation for the file style when there is only one part' => [
                'MySuperFile.php',
                LinkRenderer::PRESENTATION_FILE_SHORT,
                null,
            ],
            'Abbreviation is populated with presentation if it doesnt match known style' => [
                'MySuperClass',
                'Text to show in link',
                'Text to show in link',
            ],
            'Class name is shown with the class style when there is a full FQCN' => [
                'My\Super\Class',
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                'Class',
            ],
            'Filename is shown for the file style when there is a path' => [
                'My/Super/File.php',
                LinkRenderer::PRESENTATION_FILE_SHORT,
                'File.php',
            ],
        ];
    }
}
