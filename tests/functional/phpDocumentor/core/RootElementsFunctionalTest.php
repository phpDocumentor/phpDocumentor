<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\FunctionalTestCase;

final class RootElementsFunctionalTest extends FunctionalTestCase
{
    public function testProjectContainsTrait() : void
    {
        $this->runPHPDocWithFile(__DIR__ . '/../../assets/core/rootElements/trait.php');
        $project = $this->loadAst();

        $traitDescriptor = $project->getIndexes()->get('traits')->get('\App\Traits\Test');
        self::assertInstanceOf(TraitDescriptor::class, $traitDescriptor);

        $fileDescriptor = $project->getFiles()->get('test.php');
        self::assertInstanceOf(TraitDescriptor::class, $fileDescriptor->getTraits()->get('\App\Traits\Test'));

        ///dd($project->getNamespace()->getChildren()->getAll());

        $namespaceDescriptor = $project->getNamespace()->getChildren()->get('App')->getChildren()->get('Traits');
        self::assertInstanceOf(TraitDescriptor::class, $namespaceDescriptor->getTraits()->get('\App\Traits\Test'));
    }
}
