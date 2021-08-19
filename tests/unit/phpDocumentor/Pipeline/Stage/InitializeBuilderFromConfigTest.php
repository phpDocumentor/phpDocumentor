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

namespace phpDocumentor\Pipeline\Stage;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class InitializeBuilderFromConfigTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    public function testSetNameAndPartialsOnBuilder(): void
    {
        $partials = new Collection();
        $fixture  = new InitializeBuilderFromConfig($partials);

        $builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $builder->createProjectDescriptor()->shouldBeCalledOnce();
        $builder->setName('my-title')->shouldBeCalledOnce();
        $builder->setPartials($partials)->shouldBeCalledOnce();
        $builder->setCustomSettings([])->shouldBeCalledOnce();

        $fixture(new Payload(['phpdocumentor' => ['title' => 'my-title', 'versions' => []]], $builder->reveal()));
    }

    public function testInitializesProjectWithDocumentationSets(): void
    {
        $partials = new Collection();
        $fixture  = new InitializeBuilderFromConfig($partials);

        $builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $builder->createProjectDescriptor()->shouldBeCalledOnce();

        $fixture(
            new Payload(
                [
                    'title' => 'my-title',
                    'versions' => [
                        $this->faker()->versionSpecification(),
                    ],
                ],
                $builder->reveal()
            )
        );
    }
}
