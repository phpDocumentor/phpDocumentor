<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\DocBlock;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Generic;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor */
final class DescriptionDescriptorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getBodyTemplate
     */
    public function testBodyTemplateIsProxiedToDescription(): void
    {
        $bodyTemplate = 'my template';
        $description = new Description($bodyTemplate);

        $descriptor = new DescriptionDescriptor($description, []);

        self::assertSame($bodyTemplate, $descriptor->getBodyTemplate());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToStringRendersDescription(): void
    {
        $bodyTemplate = 'my template %s';
        $description = new Description(
            $bodyTemplate,
            [new Generic('internal', new Description('Some text'))],
        );

        $descriptor = new DescriptionDescriptor(
            $description,
            [
                new TagDescriptor(
                    'internal',
                    new DescriptionDescriptor(
                        new Description('Some text'),
                        [],
                    ),
                ),
            ],
        );

        self::assertSame((string) $description, (string) $descriptor);
    }

    /** @dataProvider replacementProvider */
    public function testTagsCanBeReplaced(TagDescriptor|null $tagDescriptorReplacement, string $expected): void
    {
        $bodyTemplate = 'my template %1$s';
        $description = new Description(
            $bodyTemplate,
            [new Generic('internal', new Description('Some text'))],
        );

        $descriptor = new DescriptionDescriptor(
            $description,
            [
                new TagDescriptor(
                    'internal',
                    new DescriptionDescriptor(
                        new Description('Some text'),
                        [],
                    ),
                ),
            ],
        );

        $descriptor->replaceTag(
            0,
            $tagDescriptorReplacement,
        );

        self::assertSame($expected, (string) $descriptor);
    }

    public function replacementProvider(): array
    {
        return [
            'replace tag' => [
                new TagDescriptor(
                    'internal',
                    new DescriptionDescriptor(
                        new Description('Replaced'),
                        [],
                    ),
                ),
                'my template {@internal Replaced}',
            ],
            'replace with null' => [
                null,
                'my template ',
            ],
        ];
    }
}
