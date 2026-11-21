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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SetVersionPass::class)]
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

    public function testGetDescription(): void
    {
        $description = $this->setVersionPass->getDescription();

        $this->assertSame('Prepare version in repository', $description);
    }

    public function testInvokeReturnsAnythingOtherThanAVersionDescriptorUnchanged(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();

        $result = $this->setVersionPass->__invoke($apiSetDescriptor);

        $this->assertSame($apiSetDescriptor, $result);
    }

    public function testInvoke(): void
    {
        $versionDescriptor = $this->faker()->versionDescriptor([]);

        $result = $this->setVersionPass->__invoke($versionDescriptor);

        self::assertSame($versionDescriptor, $this->descriptorRepository->getVersionDescriptor());
        self::assertSame($versionDescriptor, $result);
    }
}
