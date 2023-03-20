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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\RemoveSourcecode
 * @covers ::<private>
 */
final class RemoveSourcecodeTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /**
     * @covers ::execute
     */
    public function testRemovesSourceWhenDisabled(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->giveProjectDescriptor($apiSetDescriptor);
        $fixture = new RemoveSourcecode();

        $fixture->execute($projectDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNull($file->getSource());
        }
    }

    /**
     * @covers ::execute
     */
    public function testRemovesSourceWhenSourceShouldBeIncluded(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $apiSetDescriptor->getSettings()['include-source'] = true;
        $projectDescriptor = $this->giveProjectDescriptor($apiSetDescriptor);
        $fixture = new RemoveSourcecode();

        $fixture->execute($projectDescriptor);

        foreach ($apiSetDescriptor->getFiles() as $file) {
            self::assertNotNull($file->getSource());
        }
    }

    private function giveProjectDescriptor(ApiSetDescriptor $apiDescriptor): ProjectDescriptor
    {
        $projectDescriptor = $this->faker()->projectDescriptor();
        $versionDesciptor = $this->faker()->versionDescriptor([$apiDescriptor]);
        $projectDescriptor->getVersions()->add($versionDesciptor);
        $apiDescriptor->setFiles(
            DescriptorCollection::fromClassString(
                DocumentationSetDescriptor::class,
                [$this->faker()->fileDescriptor()]
            )
        );

        return $projectDescriptor;
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription(): void
    {
        $pass = new RemoveSourcecode();

        self::assertSame('Removing sourcecode from file descriptors', $pass->getDescription());
    }
}
