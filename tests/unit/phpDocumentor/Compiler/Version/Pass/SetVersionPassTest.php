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

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Version\Pass\SetVersionPass
 * @covers ::<private>
 * @covers ::__construct
 */
final class SetVersionPassTest extends TestCase
{
    use Faker;

    private SetVersionPass $setVersionPass;
    private DescriptorRepository $descriptorRepository;

    protected function setUp(): void
    {
        $this->descriptorRepository = new DescriptorRepository();
        $this->setVersionPass = new SetVersionPass($this->descriptorRepository);
    }

    /** @covers ::getDescription */
    public function testGetDescription(): void
    {
        $description = $this->setVersionPass->getDescription();

        $this->assertSame('Prepare version in repository', $description);
    }

    /** @covers ::__invoke */
    public function testInvokeReturnsAnythingOtherThanAVersionDescriptorUnchanged(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();

        $result = $this->setVersionPass->__invoke($apiSetDescriptor);

        $this->assertSame($apiSetDescriptor, $result);
    }

    /** @covers ::__invoke */
    public function testInvoke(): void
    {
        $versionDescriptor = $this->faker()->versionDescriptor([]);

        $result = $this->setVersionPass->__invoke($versionDescriptor);

        self::assertSame($versionDescriptor, $this->descriptorRepository->getVersionDescriptor());
        self::assertSame($versionDescriptor, $result);
    }
}
