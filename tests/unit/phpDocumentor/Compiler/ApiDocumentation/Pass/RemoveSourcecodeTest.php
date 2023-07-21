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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\ApiDocumentation\Pass\RemoveSourcecode
 * @covers ::<private>
 */
final class RemoveSourcecodeTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @covers ::__invoke */
    public function testRemovesSourceWhenDisabled(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor = $this->givenFiles($apiSetDescriptor);
        $apiSetDescriptor->getSettings()['include-source'] = false;
        $fixture = new RemoveSourcecode();

        $fixture->__invoke($apiSetDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNull($file->getSource());
        }
    }

    public function testRemovesSourceWhenNotSet(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor = $this->givenFiles($apiSetDescriptor);
        $apiSetDescriptor->getSettings()['include-source'] = null;
        $fixture = new RemoveSourcecode();

        $fixture->__invoke($apiSetDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNull($file->getSource());
        }
    }

    /** @covers ::__invoke */
    public function testRemovesSourceWhenSourceShouldBeIncluded(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor->getSettings()['include-source'] = true;
        $apiSetDescriptor = $this->givenFiles($apiSetDescriptor);
        $fixture = new RemoveSourcecode();

        $fixture->__invoke($apiSetDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNotNull($file->getSource());
        }
    }

    public function testSourceIsIncludedWhenFilesourceTagIsPresent(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor = $this->givenFiles($apiSetDescriptor);
        $apiSetDescriptor->getFiles()->first()->getTags()->set(
            'filesource',
            $this->faker()->fileDescriptor(),
        );
        $fixture = new RemoveSourcecode();

        $fixture->__invoke($apiSetDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNotNull($file->getSource());
        }
    }

    public function testSourceIsRemovedWhenSettingDisabledExplicitly(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor = $this->givenFiles($apiSetDescriptor);
        $apiSetDescriptor->getSettings()['include-source'] = false;
        $apiSetDescriptor->getFiles()->first()->getTags()->set(
            'filesource',
            $this->faker()->fileDescriptor(),
        );
        $fixture = new RemoveSourcecode();

        $fixture->__invoke($apiSetDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNull($file->getSource());
        }
    }

    private function givenFiles(ApiSetDescriptor $apiDescriptor): ApiSetDescriptor
    {
        $apiDescriptor->setFiles(
            DescriptorCollection::fromClassString(
                DocumentationSetDescriptor::class,
                [$this->faker()->fileDescriptor()],
            ),
        );

        return $apiDescriptor;
    }

    /** @covers ::getDescription */
    public function testGetDescription(): void
    {
        $pass = new RemoveSourcecode();

        self::assertSame('Removing sourcecode from file descriptors', $pass->getDescription());
    }
}
