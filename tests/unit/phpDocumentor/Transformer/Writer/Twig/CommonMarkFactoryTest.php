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
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\CommonMarkFactory
 */
final class CommonMarkFactoryTest extends TestCase
{
    /**
     * @covers ::createConverter
     */
    public function testCreateAddsExtensions() : void
    {
        $extension = new DisallowedRawHtmlExtension();

        $factory = new CommonMarkFactory();
        $converter = $factory->createConverter([$extension]);
        $extensions = $converter->getEnvironment()->getExtensions();

        self::assertContains($extension, $extensions);
    }
}
