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

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CommonMarkFactory::class)]
final class CommonMarkFactoryTest extends TestCase
{
    public function testCreateAddsExtensions(): void
    {
        $extension = new DisallowedRawHtmlExtension();

        $factory = new CommonMarkFactory();
        $converter = $factory->createConverter([$extension]);
        $extensions = $converter->getEnvironment()->getExtensions();

        self::assertContains($extension, $extensions);
    }
}
