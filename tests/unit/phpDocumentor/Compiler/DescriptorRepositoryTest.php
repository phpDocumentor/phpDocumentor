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

namespace phpDocumentor\Compiler;

use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/** @covers \phpDocumentor\Compiler\DescriptorRepository */
final class DescriptorRepositoryTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    private Fqsen $fqsen;
    private DescriptorRepository $descriptorRepository;

    protected function setUp(): void
    {
        $this->fqsen = self::faker()->fqsen();
        $this->descriptorRepository = new DescriptorRepository();
    }

    /**
     * @uses \phpDocumentor\Descriptor\VersionDescriptor
     * @uses \phpDocumentor\Descriptor\Collection
     */
    public function testSetVersionDescriptorMethod(): void
    {
        $versionDescriptor = self::faker()->versionDescriptor([]);

        $this->descriptorRepository->setVersionDescriptor($versionDescriptor);

        $this->assertSame(
            $versionDescriptor,
            $this->descriptorRepository->getVersionDescriptor(),
            'The set and retrieved version descriptors must be the same.',
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\VersionDescriptor
     * @uses \phpDocumentor\Descriptor\Collection
     */
    public function testGetVersionDescriptorMethod(): void
    {
        $versionDescriptor = self::faker()->versionDescriptor([]);

        $this->descriptorRepository->setVersionDescriptor($versionDescriptor);

        $this->assertSame(
            $versionDescriptor,
            $this->descriptorRepository->getVersionDescriptor(),
        );
    }

    /**
     * @uses phpDocumentor\Descriptor\VersionDescriptor
     * @uses \phpDocumentor\Descriptor\ApiSetDescriptor
     */
    public function testItCanFindDescriptorByFqsen(): void
    {
        $classDescriptor = self::faker()->classDescriptor($this->fqsen);

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $apiSetDescriptor->getIndex('elements')->set((string) $this->fqsen, $classDescriptor);

        $this->descriptorRepository->setVersionDescriptor(self::faker()->versionDescriptor([$apiSetDescriptor]));

        $this->assertSame(
            $classDescriptor,
            $this->descriptorRepository->findDescriptorByFqsen($this->fqsen),
        );
    }

    /**
     * @uses phpDocumentor\Descriptor\VersionDescriptor
     * @uses \phpDocumentor\Descriptor\ApiSetDescriptor
     */
    public function testItReturnsNullWhenNoDescriptorIsFoundByFqsen(): void
    {
        $this->descriptorRepository->setVersionDescriptor(
            self::faker()->versionDescriptor([self::faker()->apiSetDescriptor()]),
        );

        $this->assertNull($this->descriptorRepository->findDescriptorByFqsen($this->fqsen));
    }

    /**
     * @uses phpDocumentor\Descriptor\VersionDescriptor
     * @uses \phpDocumentor\Descriptor\ApiSetDescriptor
     */
    public function testItCanFindDescriptorByTypeAndFqsen(): void
    {
        $classDescriptor = self::faker()->classDescriptor($this->fqsen);

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $apiSetDescriptor
            ->getIndex('classes')
            ->set((string) $this->fqsen, $classDescriptor);

        $this->descriptorRepository
            ->setVersionDescriptor(self::faker()->versionDescriptor([$apiSetDescriptor]));

        $this->assertSame(
            $classDescriptor,
            $this->descriptorRepository->findDescriptorByTypeAndFqsen('class', $this->fqsen),
        );
    }
}
