<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use PHPUnit\Framework\TestCase;

final class InitializeBuilderFromConfigTest extends TestCase
{
    public function testSetNameAndPartialsOnBuilder() : void
    {
        $partials = new Collection();
        $fixture = new InitializeBuilderFromConfig($partials);

        $builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $builder->createProjectDescriptor()->shouldBeCalledOnce();
        $builder->setName('my-title')->shouldBeCalledOnce();
        $builder->setPartials($partials)->shouldBeCalledOnce();

        $fixture(new Payload(['phpdocumentor' => ['title' => 'my-title']], $builder->reveal()));
    }
}
