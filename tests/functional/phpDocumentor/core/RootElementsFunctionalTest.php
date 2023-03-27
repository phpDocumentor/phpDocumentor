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

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\FunctionalTestCase;

final class RootElementsFunctionalTest extends FunctionalTestCase
{
    public function testProjectContainsTrait() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/rootElements/trait.php');
        $project = $this->loadAst();

        $versions = $project->getVersions();
        $this->assertCount(1, $versions);

        $apiSets = $versions->first()->getDocumentationSets()->filter(ApiSetDescriptor::class);
        $this->assertCount(1, $apiSets);

        /** @var ApiSetDescriptor $apiSet */
        $apiSet = $apiSets->first();

        $traitDescriptor = $apiSet->getIndexes()->get('traits')->get('\App\Traits\Test');
        self::assertInstanceOf(TraitDescriptor::class, $traitDescriptor);

        $fileDescriptor = $apiSet->getFiles()->get('test.php');
        self::assertInstanceOf(TraitDescriptor::class, $fileDescriptor->getTraits()->get('\App\Traits\Test'));

        $namespaceDescriptor = $apiSet->getNamespace()->getChildren()->get('App')->getChildren()->get('Traits');
        self::assertInstanceOf(TraitDescriptor::class, $namespaceDescriptor->getTraits()->get('\App\Traits\Test'));
    }
}
