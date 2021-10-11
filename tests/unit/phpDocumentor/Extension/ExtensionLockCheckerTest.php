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

namespace phpDocumentor\Extension;

use org\bovigo\vfs\vfsStream;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;

use function time;

/**
 * @coversDefaultClass \phpDocumentor\Extension\ExtensionLockChecker
 */
final class ExtensionLockCheckerTest extends TestCase
{
    use Faker;

    /**
     * @covers ::supports
     * @covers ::__construct
     */
    public function testSupportsExtensionResource(): void
    {
        $root = vfsStream::setup();
        $extensionLockChecker = new ExtensionLockChecker([]);

        self::assertTrue($extensionLockChecker->supports(new ExtensionsResource([])));
        self::assertFalse($extensionLockChecker->supports(new FileResource($root->url())));
    }

    /**
     * @covers ::isFresh
     * @covers ::__construct
     */
    public function testIsNotFreshWhenChangesDetected(): void
    {
        $manifest = $this->faker()->extensionManifest();

        $extensionLockChecker = new ExtensionLockChecker([]);
        self::assertFalse($extensionLockChecker->isFresh(new ExtensionsResource([$manifest]), time()));
    }

    /**
     * @covers ::isFresh
     * @covers ::__construct
     */
    public function testIsNotFreshWhenExtensionIsChanged(): void
    {
        $manifest = $this->faker()->extensionManifest();
        $manifest20 = $this->faker()->extensionManifest('2.0.0');

        $extensionLockChecker = new ExtensionLockChecker([$manifest]);
        self::assertFalse($extensionLockChecker->isFresh(new ExtensionsResource([$manifest20]), time()));
    }

    /**
     * @covers ::isFresh
     * @covers ::__construct
     */
    public function testIsFreshWhenWhenEmpty(): void
    {
        $extensionLockChecker = new ExtensionLockChecker([]);
        self::assertTrue($extensionLockChecker->isFresh(new ExtensionsResource([]), time()));
    }
}
